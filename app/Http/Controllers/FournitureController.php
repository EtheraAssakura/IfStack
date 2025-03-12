<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Fourniture;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FournitureController extends Controller
{
  public function index()
  {
    $fournitures = Fourniture::with(['categorie', 'fournisseurs'])->get();
    return view('fournitures.index', compact('fournitures'));
  }

  public function create()
  {
    $categories = Categorie::all();
    $fournisseurs = Fournisseur::all();
    return view('fournitures.create', compact('categories', 'fournisseurs'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'nom' => 'required|string|max:255',
      'reference_isfac' => 'required|string|unique:fournitures',
      'conditionnement' => 'required|string|max:255',
      'url_catalogue' => 'nullable|url',
      'seuil_alerte_global' => 'required|integer|min:0',
      'categorie_id' => 'required|exists:categories,id',
      'image' => 'nullable|image|max:2048',
      'fournisseurs' => 'required|array',
      'fournisseurs.*.id' => 'required|exists:fournisseurs,id',
      'fournisseurs.*.reference' => 'required|string',
      'fournisseurs.*.prix' => 'required|numeric|min:0',
    ]);

    if ($request->hasFile('image')) {
      $path = $request->file('image')->store('fournitures', 'public');
      $validated['image_url'] = Storage::url($path);
    }

    $fourniture = Fourniture::create($validated);

    foreach ($request->fournisseurs as $fournisseur) {
      $fourniture->fournisseurs()->attach($fournisseur['id'], [
        'reference_fournisseur' => $fournisseur['reference'],
        'prix_unitaire' => $fournisseur['prix'],
      ]);
    }

    return redirect()->route('fournitures.index')
      ->with('success', 'Fourniture créée avec succès.');
  }

  public function edit(Fourniture $fourniture)
  {
    $categories = Categorie::all();
    $fournisseurs = Fournisseur::all();
    return view('fournitures.edit', compact('fourniture', 'categories', 'fournisseurs'));
  }

  public function update(Request $request, Fourniture $fourniture)
  {
    $validated = $request->validate([
      'nom' => 'required|string|max:255',
      'reference_isfac' => ['required', 'string', Rule::unique('fournitures')->ignore($fourniture)],
      'conditionnement' => 'required|string|max:255',
      'url_catalogue' => 'nullable|url',
      'seuil_alerte_global' => 'required|integer|min:0',
      'categorie_id' => 'required|exists:categories,id',
      'image' => 'nullable|image|max:2048',
      'fournisseurs' => 'required|array',
      'fournisseurs.*.id' => 'required|exists:fournisseurs,id',
      'fournisseurs.*.reference' => 'required|string',
      'fournisseurs.*.prix' => 'required|numeric|min:0',
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
          'reference_fournisseur' => $item['reference'],
          'prix_unitaire' => $item['prix'],
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
