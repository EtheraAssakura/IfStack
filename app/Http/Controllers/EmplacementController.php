<?php

namespace App\Http\Controllers;

use App\Models\Emplacement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmplacementController extends Controller
{
  public function index()
  {
    $emplacements = Emplacement::with('etablissement')->get();
    return view('emplacements.index', compact('emplacements'));
  }

  public function create()
  {
    return view('emplacements.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'nom' => 'required|string|max:255',
      'description' => 'nullable|string',
      'etablissement_id' => 'required|exists:etablissements,id',
      'photo' => 'nullable|image|max:2048'
    ]);

    $emplacement = Emplacement::create($validated);

    if ($request->hasFile('photo')) {
      $path = $request->file('photo')->store('emplacements', 'public');
      $emplacement->update(['photo_path' => $path]);
    }

    return redirect()->route('emplacements.index')
      ->with('success', 'Emplacement créé avec succès.');
  }

  public function show(Emplacement $emplacement)
  {
    return view('locations.show', compact('emplacement'));
  }

  public function edit(Emplacement $emplacement)
  {
    return view('emplacements.edit', compact('emplacement'));
  }

  public function update(Request $request, Emplacement $emplacement)
  {
    $validated = $request->validate([
      'nom' => 'required|string|max:255',
      'description' => 'nullable|string',
      'etablissement_id' => 'required|exists:etablissements,id'
    ]);

    $emplacement->update($validated);

    return redirect()->route('emplacements.index')
      ->with('success', 'Emplacement mis à jour avec succès.');
  }

  public function destroy(Emplacement $emplacement)
  {
    if ($emplacement->photo_path) {
      Storage::disk('public')->delete($emplacement->photo_path);
    }

    $emplacement->delete();

    return redirect()->route('emplacements.index')
      ->with('success', 'Emplacement supprimé avec succès.');
  }

  public function uploadPhoto(Request $request, Emplacement $emplacement)
  {
    $request->validate([
      'photo' => 'required|image|max:2048'
    ]);

    if ($emplacement->photo_path) {
      Storage::disk('public')->delete($emplacement->photo_path);
    }

    $path = $request->file('photo')->store('emplacements', 'public');
    $emplacement->update(['photo_path' => $path]);

    return redirect()->route('locations.show', $emplacement)
      ->with('success', 'Photo mise à jour avec succès.');
  }
}
