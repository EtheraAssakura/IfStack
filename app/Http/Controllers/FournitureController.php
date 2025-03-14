<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Fourniture;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FournitureController extends Controller
{
  public function index(): Response
  {
    $fournitures = Fourniture::with(['categorie', 'fournisseurs'])->get();

    $mappedFournitures = $fournitures->map(function ($fourniture) {
      $mapped = [
        'id' => $fourniture->id,
        'name' => $fourniture->name,
        'reference' => $fourniture->reference,
        'packaging' => $fourniture->packaging,
        'catalog_url' => $fourniture->catalog_url,
        'category' => [
          'id' => $fourniture->categorie->id,
          'name' => $fourniture->categorie->name,
        ],
        'fournisseurs' => $fourniture->fournisseurs->map(function ($fournisseur) {
          return [
            'id' => $fournisseur->id,
            'name' => $fournisseur->name,
            'catalog_url' => $fournisseur->catalog_url,
            'pivot' => [
              'supplier_reference' => $fournisseur->pivot->supplier_reference,
              'unit_price' => $fournisseur->pivot->unit_price,
              'catalog_url' => $fournisseur->pivot->catalog_url,
            ],
          ];
        }),
      ];

      return $mapped;
    });

    return Inertia::render('Fournitures/Index', [
      'fournitures' => $mappedFournitures
    ]);
  }

  public function create(): Response
  {
    $categories = Categorie::all();
    $fournisseurs = Fournisseur::all();
    return Inertia::render('Fournitures/Create', [
      'categories' => $categories,
      'fournisseurs' => $fournisseurs
    ]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'reference' => ['required', 'string', Rule::unique('supplies')],
      'packaging' => 'required|string|max:255',
      'catalog_url' => 'nullable|url',
      'category_id' => 'required|exists:categories,id',
      'image' => 'nullable|image|max:2048',
      'fournisseurs' => 'required|array',
      'fournisseurs.*.id' => 'required|exists:suppliers,id',
      'fournisseurs.*.reference' => 'required|string',
      'fournisseurs.*.prix' => 'required|numeric|min:0',
      'fournisseurs.*.catalog_url' => 'nullable|url',
    ]);

    if ($request->hasFile('image')) {
      $path = $request->file('image')->store('fournitures', 'public');
      $validated['image_url'] = Storage::url($path);
    }

    $fourniture = Fourniture::create($validated);

    foreach ($request->fournisseurs as $fournisseur) {
      $fourniture->fournisseurs()->attach($fournisseur['id'], [
        'supplier_reference' => $fournisseur['reference'],
        'unit_price' => $fournisseur['prix'],
        'catalog_url' => $fournisseur['catalog_url'] ?? null,
      ]);
    }

    return redirect()->route('fournitures.index')
      ->with('success', 'Fourniture créée avec succès.');
  }

  public function show(Fourniture $fourniture): Response
  {
    $fourniture->load([
      'categorie',
      'fournisseurs',
      'stocks.emplacement.etablissement'
    ]);
    return Inertia::render('Fournitures/Show', [
      'fourniture' => [
        'id' => $fourniture->id,
        'name' => $fourniture->name,
        'reference' => $fourniture->reference,
        'packaging' => $fourniture->packaging,
        'image_url' => $fourniture->image_url,
        'category' => $fourniture->categorie ? [
          'id' => $fourniture->categorie->id,
          'name' => $fourniture->categorie->name,
        ] : null,
        'fournisseurs' => $fourniture->fournisseurs->map(function ($fournisseur) {
          return [
            'id' => $fournisseur->id,
            'name' => $fournisseur->name,
            'catalog_url' => $fournisseur->catalog_url,
            'pivot' => [
              'supplier_reference' => $fournisseur->pivot->supplier_reference,
              'unit_price' => $fournisseur->pivot->unit_price,
              'catalog_url' => $fournisseur->pivot->catalog_url,
            ],
          ];
        }),
        'stocks' => $fourniture->stocks->map(function ($stock) {
          return [
            'id' => $stock->id,
            'estimated_quantity' => $stock->estimated_quantity,
            'local_alert_threshold' => $stock->local_alert_threshold,
            'location' => $stock->emplacement ? [
              'id' => $stock->emplacement->id,
              'name' => $stock->emplacement->name,
              'site' => $stock->emplacement->etablissement ? [
                'id' => $stock->emplacement->etablissement->id,
                'name' => $stock->emplacement->etablissement->name,
              ] : null,
            ] : null,
          ];
        }),
      ],
    ]);
  }

  public function edit(Fourniture $fourniture): Response
  {
    $fourniture->load(['categorie', 'fournisseurs']);
    $categories = Categorie::all();
    $fournisseurs = Fournisseur::all();

    Log::info('Données de la fourniture:', [
      'id' => $fourniture->id,
      'category_id' => $fourniture->category_id,
      'categorie' => $fourniture->categorie,
    ]);

    $data = [
      'fourniture' => [
        'id' => $fourniture->id,
        'name' => $fourniture->name,
        'reference' => $fourniture->reference,
        'packaging' => $fourniture->packaging,
        'catalog_url' => $fourniture->catalog_url,
        'image_url' => $fourniture->image_url,
        'category_id' => $fourniture->category_id,
        'category' => $fourniture->categorie ? [
          'id' => $fourniture->categorie->id,
          'name' => $fourniture->categorie->name,
        ] : null,
        'fournisseurs' => $fourniture->fournisseurs->map(function ($fournisseur) {
          return [
            'id' => $fournisseur->id,
            'name' => $fournisseur->name,
            'catalog_url' => $fournisseur->catalog_url,
            'pivot' => [
              'supplier_reference' => $fournisseur->pivot->supplier_reference,
              'unit_price' => $fournisseur->pivot->unit_price,
              'catalog_url' => $fournisseur->pivot->catalog_url,
            ],
          ];
        }),
      ],
      'categories' => $categories,
      'fournisseurs' => $fournisseurs,
    ];

    Log::info('Données envoyées à la vue:', $data);

    return Inertia::render('Fournitures/Edit', $data);
  }

  public function update(Request $request, Fourniture $fourniture)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'reference' => ['required', 'string', Rule::unique('supplies')->ignore($fourniture)],
      'packaging' => 'required|string|max:255',
      'catalog_url' => 'nullable|url',
      'category_id' => 'required|exists:categories,id',
      'image' => 'nullable|image|max:2048',
      'fournisseurs' => 'required|array',
      'fournisseurs.*.id' => 'required|exists:suppliers,id',
      'fournisseurs.*.reference' => 'required|string',
      'fournisseurs.*.prix' => 'required|numeric|min:0',
      'fournisseurs.*.catalog_url' => 'nullable|url',
    ]);

    if ($request->hasFile('image')) {
      if ($fourniture->image_url) {
        Storage::delete(str_replace('/storage/', 'public/', $fourniture->image_url));
      }
      $path = $request->file('image')->store('fournitures', 'public');
      $validated['image_url'] = Storage::url($path);
    }

    $fourniture->update($validated);

    $fourniture->fournisseurs()->sync(
      collect($request->fournisseurs)->mapWithKeys(function ($item) {
        return [$item['id'] => [
          'supplier_reference' => $item['reference'],
          'unit_price' => $item['prix'],
          'catalog_url' => $item['catalog_url'] ?? null,
        ]];
      })
    );

    return redirect()->route('fournitures.index')
      ->with('success', 'Fourniture mise à jour avec succès.');
  }

  public function destroy(Fourniture $fourniture)
  {
    if ($fourniture->image_url) {
      Storage::delete(str_replace('/storage/', 'public/', $fourniture->image_url));
    }

    $fourniture->delete();

    return redirect()->route('fournitures.index')
      ->with('success', 'Fourniture supprimée avec succès.');
  }
}
