<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplyController extends Controller
{
    public function index(): JsonResponse
    {
        $supplies = Supply::with(['category', 'suppliers'])->get();
        return response()->json($supplies);
    }

    public function show(Supply $supply): JsonResponse
    {
        $supply->load(['category', 'suppliers', 'stockItems.location']);
        return response()->json($supply);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => 'required|string|max:50|unique:supplies',
            'packaging' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'alert_threshold' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
            'catalog_url' => 'nullable|url'
        ]);

        $supply = Supply::create($validated);
        return response()->json($supply, 201);
    }

    public function update(Request $request, Supply $supply): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'reference' => 'string|max:50|unique:supplies,reference,' . $supply->id,
            'packaging' => 'string|max:255',
            'category_id' => 'exists:categories,id',
            'alert_threshold' => 'integer|min:0',
            'image_url' => 'nullable|url',
            'catalog_url' => 'nullable|url'
        ]);

        $supply->update($validated);
        return response()->json($supply);
    }

    public function destroy(Supply $supply): JsonResponse
    {
        $supply->delete();
        return response()->json(null, 204);
    }

    public function getStock(Supply $supply): JsonResponse
    {
        $stockItems = $supply->stockItems()
            ->with('location.site')
            ->get()
            ->map(function ($item) {
                return [
                    'location' => $item->location->name,
                    'site' => $item->location->site->name,
                    'quantity' => $item->estimated_quantity,
                    'alert_threshold' => $item->local_alert_threshold
                ];
            });

        return response()->json($stockItems);
    }

    public function getMovements(Supply $supply): JsonResponse
    {
        $movements = $supply->stockMovements()
            ->with(['location', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($movements);
    }

    public function getSuppliers(Supply $supply): JsonResponse
    {
        $suppliers = $supply->suppliers()
            ->with('pivot')
            ->get()
            ->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'reference' => $supplier->pivot->supplier_reference,
                    'unit_price' => $supplier->pivot->unit_price,
                    'catalog_url' => $supplier->catalog_url
                ];
            });

        return response()->json($suppliers);
    }

    public function searchByReference(Request $request): JsonResponse
    {
        $reference = $request->get('reference');
        $supplies = Supply::where('reference', 'like', "%{$reference}%")
            ->with(['category', 'suppliers'])
            ->get();

        return response()->json($supplies);
    }
}
