<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Site;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LocationController extends Controller
{
  public function index()
  {
    return Inertia::render('Locations/Index', [
      'locations' => Location::with('site')
        ->withCount('stockItems')
        ->get()
        ->map(function ($location) {
          return [
            'id' => $location->id,
            'name' => $location->name,
            'description' => $location->description,
            'site' => $location->site->name,
            'stock_items_count' => $location->stock_items_count,
            'photo_url' => $location->photo_path ? Storage::url($location->photo_path) : null,
            'qr_code' => $location->qr_code,
          ];
        })
    ]);
  }

  public function create()
  {
    return Inertia::render('Locations/Create', [
      'sites' => Site::select('id', 'name')->get()
    ]);
  }

  public function store(Request $request, Etablissement $etablissement)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'photo' => 'nullable|image|max:2048'
    ]);

    $location = new Location([
      'name' => $validated['name'],
      'description' => $validated['description'],
      'etablissement_id' => $etablissement->id,
      'qr_code' => Str::uuid()->toString()
    ]);

    if ($request->hasFile('photo')) {
      $path = $request->file('photo')->store('locations', 'public');
      $location->photo_path = $path;
    }

    $location->save();

    return redirect()->back()->with('success', 'Emplacement créé avec succès.');
  }

  public function edit(Location $location)
  {
    return Inertia::render('Locations/Edit', [
      'location' => [
        'id' => $location->id,
        'name' => $location->name,
        'description' => $location->description,
        'site_id' => $location->site_id,
        'photo_url' => $location->photo_path ? Storage::url($location->photo_path) : null,
      ],
      'sites' => Site::select('id', 'name')->get()
    ]);
  }

  public function update(Request $request, Etablissement $etablissement, Location $location)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'photo' => 'nullable|image|max:2048'
    ]);

    if ($location->etablissement_id !== $etablissement->id) {
      abort(403);
    }

    $location->fill([
      'name' => $validated['name'],
      'description' => $validated['description']
    ]);

    if ($request->hasFile('photo')) {
      if ($location->photo_path) {
        Storage::disk('public')->delete($location->photo_path);
      }
      $path = $request->file('photo')->store('locations', 'public');
      $location->photo_path = $path;
    }

    $location->save();

    return redirect()->back()->with('success', 'Emplacement mis à jour avec succès.');
  }

  public function destroy(Etablissement $etablissement, Location $location)
  {
    if ($location->etablissement_id !== $etablissement->id) {
      abort(403);
    }

    if ($location->photo_path) {
      Storage::disk('public')->delete($location->photo_path);
    }

    $location->delete();

    return redirect()->back()->with('success', 'Emplacement supprimé avec succès.');
  }

  public function show(Etablissement $etablissement, Location $location)
  {
    if ($location->etablissement_id !== $etablissement->id) {
      abort(403);
    }

    return Inertia::render('Locations/Show', [
      'location' => [
        'id' => $location->id,
        'name' => $location->name,
        'description' => $location->description,
        'etablissement' => [
          'id' => $etablissement->id,
          'name' => $etablissement->name
        ],
        'photo_url' => $location->photo_path ? Storage::url($location->photo_path) : null,
        'stock_items' => $location->stocks()
          ->with(['fourniture', 'fourniture.category'])
          ->get()
          ->map(function ($item) {
            return [
              'id' => $item->id,
              'fourniture' => [
                'name' => $item->fourniture->name,
                'reference' => $item->fourniture->reference,
                'category' => $item->fourniture->category->name
              ],
              'estimated_quantity' => $item->estimated_quantity,
              'local_alert_threshold' => $item->local_alert_threshold,
              'last_update' => $item->updated_at->format('d/m/Y H:i')
            ];
          })
      ]
    ]);
  }

  public function uploadPhoto(Request $request, Etablissement $etablissement, Location $location)
  {
    $request->validate([
      'photo' => 'required|image|max:2048'
    ]);

    if ($location->etablissement_id !== $etablissement->id) {
      abort(403);
    }

    if ($location->photo_path) {
      Storage::disk('public')->delete($location->photo_path);
    }

    $path = $request->file('photo')->store('locations', 'public');
    $location->photo_path = $path;
    $location->save();

    return redirect()->back()->with('success', 'Photo mise à jour avec succès.');
  }
}
