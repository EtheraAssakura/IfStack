<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Site;
use App\Models\Etablissement;
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
        Log::info('Début de la création de l\'emplacement', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method()
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            Log::info('Validation réussie', ['validated_data' => $validated]);

            $location = new Location([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'site_id' => $etablissement->id,
                'qr_code' => Str::uuid()->toString()
            ]);

            $location->save();

            return redirect()->back()->with('success', 'Emplacement créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'emplacement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la création de l\'emplacement.');
        }
    }

    public function edit(Location $location)
    {
        return Inertia::render('Locations/Edit', [
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
                'description' => $location->description,
                'site_id' => $location->site_id
            ],
            'sites' => Site::select('id', 'name')->get()
        ]);
    }

    public function update(Request $request, Etablissement $etablissement, Location $location)
    {
        Log::info('Début de la mise à jour de l\'emplacement', [
            'location_id' => $location->id,
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method()
        ]);

        try {
            if ($location->site_id !== $etablissement->id) {
                abort(403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            Log::info('Validation réussie', ['validated_data' => $validated]);

            $location->fill([
                'name' => $validated['name'],
                'description' => $validated['description']
            ]);

            $location->save();

            return redirect()->back()->with('success', 'Emplacement mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'emplacement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour de l\'emplacement.');
        }
    }

    public function destroy(Etablissement $etablissement, Location $location)
    {
        if ($location->site_id !== $etablissement->id) {
            abort(403);
        }

        $location->delete();

        return redirect()->back()->with('success', 'Emplacement supprimé avec succès.');
    }

    public function show(Etablissement $etablissement, Location $location)
    {
        if ($location->site_id !== $etablissement->id) {
            abort(403);
        }

        // Générer l'URL pour le QR code
        $url = route('etablissements.locations.show', [$etablissement->id, $location->id]);
        $qrCodePath = 'qrcodes/location_' . $location->id . '.svg';
        $qrCodeUrl = $this->generateQrCode($url, $qrCodePath);

        \Log::info('QR Code URL générée:', ['url' => $qrCodeUrl]);

        return Inertia::render('Locations/Show', [
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
                'description' => $location->description,
                'etablissement' => [
                    'id' => $etablissement->id,
                    'name' => $etablissement->name
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

    public function uploadPhoto(Request $request, Etablissement $etablissement, Location $location)
    {
        $request->validate([
            'photo' => 'required|image|max:2048'
        ]);

        if ($location->photo_path) {
            Storage::disk('public')->delete($location->photo_path);
        }

        $path = $request->file('photo')->store('locations', 'public');
        $location->photo_path = $path;
        $location->save();

        return redirect()->back()->with('success', 'Photo mise à jour avec succès.');
    }
}
