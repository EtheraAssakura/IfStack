<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Supply;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SupplyController extends Controller
{
    public function index(): Response
    {
        $supplies = Supply::with(['category', 'suppliers'])->get();

        $mappedSupplies = $supplies->map(function ($supply) {
            return [
                'id' => $supply->id,
                'name' => $supply->name,
                'reference' => $supply->reference,
                'packaging' => $supply->packaging,
                'category' => [
                    'id' => $supply->category->id,
                    'name' => $supply->category->name,
                ],
                'suppliers' => $supply->suppliers->map(function ($supplier) {
                    return [
                        'id' => $supplier->id,
                        'name' => $supplier->name,
                        'catalog_url' => $supplier->catalog_url,
                        'pivot' => [
                            'supplier_reference' => $supplier->pivot->supplier_reference,
                            'unit_price' => $supplier->pivot->unit_price,
                            'catalog_url' => $supplier->pivot->catalog_url,
                        ],
                    ];
                })->values()->all(),
            ];
        });

        return Inertia::render('Supplies/Index', [
            'supplies' => $mappedSupplies
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Supplies/Create', [
            'categories' => Category::all(),
            'suppliers' => Supplier::all()
        ]);
    }

    public function store(Request $request)
    {

        if ($request->has('suppliers')) {
            $suppliers = json_decode($request->suppliers, true);
            $request->merge(['suppliers' => $suppliers]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => 'required|string|max:255|unique:supplies',
            'packaging' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'suppliers' => 'required|array',
            'suppliers.*.id' => 'required|exists:suppliers,id',
            'suppliers.*.supplier_reference' => 'nullable|string|max:255',
            'suppliers.*.unit_price' => 'nullable|numeric|min:0',
            'suppliers.*.catalog_url' => 'nullable|url',
            'image' => 'nullable|image|max:2048'
        ]);

        $supply = Supply::create([
            'name' => $validated['name'],
            'reference' => $validated['reference'],
            'packaging' => $validated['packaging'],
            'category_id' => $validated['category_id'],
            'image_url' => null
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('supplies', 'public');
            $supply->update(['image_url' => $path]);
        }

        foreach ($validated['suppliers'] as $supplier) {
            $supply->suppliers()->attach($supplier['id'], [
                'supplier_reference' => $supplier['supplier_reference'],
                'unit_price' => $supplier['unit_price'],
                'catalog_url' => $supplier['catalog_url']
            ]);
        }

        return redirect()->route('supplies.index')
            ->with('success', 'Fourniture créée avec succès.');
    }

    public function show(Supply $supply): Response
    {
        return Inertia::render('Supplies/Show', [
            'supply' => $supply->load(['category', 'suppliers', 'stockItems.location.site'])
        ]);
    }

    public function edit(Supply $supply): Response
    {
        return Inertia::render('Supplies/Edit', [
            'supply' => $supply->load(['category', 'suppliers']),
            'categories' => Category::all(),
            'suppliers' => Supplier::all()
        ]);
    }

    public function update(Request $request, Supply $supply)
    {


        // Convertir la chaîne JSON des fournisseurs en tableau
        if ($request->has('suppliers')) {
            $suppliers = json_decode($request->suppliers, true);
            $request->merge(['suppliers' => $suppliers]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => ['required', 'string', 'max:255', Rule::unique('supplies')->ignore($supply->id)],
            'packaging' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'suppliers' => 'required|array',
            'suppliers.*.id' => 'required|exists:suppliers,id',
            'suppliers.*.supplier_reference' => 'required|string|max:255',
            'suppliers.*.unit_price' => 'required|numeric|min:0',
            'suppliers.*.catalog_url' => 'nullable|url',
            'image' => 'nullable|image|max:2048'
        ]);

        $supply->update([
            'name' => $validated['name'],
            'reference' => $validated['reference'],
            'packaging' => $validated['packaging'],
            'category_id' => $validated['category_id']
        ]);

        if ($request->hasFile('image')) {
            if ($supply->image_url) {
                Storage::disk('public')->delete($supply->image_url);
            }
            $path = $request->file('image')->store('supplies', 'public');
            $supply->update(['image_url' => $path]);
        }

        $supply->suppliers()->sync(
            collect($validated['suppliers'])->mapWithKeys(function ($supplier) {
                return [$supplier['id'] => [
                    'supplier_reference' => $supplier['supplier_reference'],
                    'unit_price' => $supplier['unit_price'],
                    'catalog_url' => $supplier['catalog_url']
                ]];
            })->all()
        );

        return redirect()->route('supplies.index')
            ->with('success', 'Fourniture mise à jour avec succès.');
    }

    public function destroy(Supply $supply)
    {
        if ($supply->image_url) {
            Storage::disk('public')->delete($supply->image_url);
        }
        $supply->delete();

        return redirect()->route('supplies.index')
            ->with('success', 'Fourniture supprimée avec succès.');
    }

    public function removeImage(Supply $supply)
    {
        if ($supply->image_url) {
            Storage::disk('public')->delete($supply->image_url);
            $supply->update(['image_url' => null]);
        }

        return redirect()->route('supplies.edit', $supply)
            ->with('success', 'Image supprimée avec succès.');
    }
}
