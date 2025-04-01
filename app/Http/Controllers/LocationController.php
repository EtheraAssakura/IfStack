<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Site;
use App\Traits\GeneratesQrCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    use GeneratesQrCodes;

    public function index()
    {
        $locations = Location::with('site')->get();
        return Inertia::render('Locations/Index', [
            'locations' => $locations
        ]);
    }

    public function create()
    {
        $sites = Site::all();
        return Inertia::render('Locations/Create', [
            'sites' => $sites
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'site_id' => 'required|exists:sites,id',
            'photo' => 'nullable|image|max:2048'
        ]);

        $location = Location::create($validated);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('locations', 'public');
            $location->update(['photo_path' => $path]);
        }

        return redirect()->route('sites.locations.show', $location->site_id)
            ->with('success', 'Emplacement créé avec succès.');
    }

    public function show(Site $site, Location $location)
    {

        $url = route('welcome', ['locationId' => $location->id]);
        $qrCodePath = 'qrcodes/location_' . $location->id . '.svg';
        $qrCodeUrl = $this->generateQrCode($url, $qrCodePath);

        return Inertia::render('Locations/Show', [
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
                'description' => $location->description,
                'site' => [
                    'id' => $site->id,
                    'name' => $site->name
                ],
                'qr_code_url' => $qrCodeUrl,
                'stock_items' => $location->stockItems()
                    ->with(['supply', 'supply.category'])
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'supply' => [
                                'name' => $item->supply->name,
                                'reference' => $item->supply->reference,
                                'category' => $item->supply->category->name
                            ],
                            'estimated_quantity' => $item->estimated_quantity,
                            'local_alert_threshold' => $item->local_alert_threshold,
                            'last_update' => $item->updated_at->format('d/m/Y H:i')
                        ];
                    })
            ]
        ]);
    }

    public function edit(Location $location)
    {
        $sites = Site::all();
        return Inertia::render('Locations/Edit', [
            'location' => $location,
            'sites' => $sites
        ]);
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'site_id' => 'required|exists:sites,id',
            'photo' => 'nullable|image|max:2048'
        ]);

        $location->update($validated);

        if ($request->hasFile('photo')) {
            if ($location->photo_path) {
                Storage::disk('public')->delete($location->photo_path);
            }
            $path = $request->file('photo')->store('locations', 'public');
            $location->update(['photo_path' => $path]);
        }

        return redirect()->back()->with('success', 'Emplacement mis à jour avec succès.');
    }

    public function destroy(Site $site, Location $location)
    {
        if ($location->site_id !== $site->id) {
            abort(403);
        }

        $location->delete();

        return redirect()->back()->with('success', 'Emplacement supprimé avec succès.');
    }

    public function uploadPhoto(Request $request, Location $location)
    {
        $request->validate([
            'photo' => 'required|image|max:2048'
        ]);

        if ($location->photo_path) {
            Storage::disk('public')->delete($location->photo_path);
        }

        $path = $request->file('photo')->store('locations', 'public');
        $location->update(['photo_path' => $path]);

        return redirect()->route('sites.locations.show', [$location->site_id, $location->id])
            ->with('success', 'Photo mise à jour avec succès.');
    }
}
