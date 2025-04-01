<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::withCount(['locations'])->get();
        return Inertia::render('Sites/Index', [
            'sites' => $sites
        ]);
    }

    public function create()
    {
        return Inertia::render('Sites/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'plan' => 'nullable|image|max:5120', // 5MB max
            'is_headquarters' => 'boolean'
        ]);

        // Générer le slug à partir du nom
        $validated['slug'] = 'isfac-' . strtolower(str_replace(' ', '-', $validated['name']));

        $site = Site::create($validated);

        if ($request->hasFile('plan')) {
            $path = $request->file('plan')->store('plans', 'public');
            $site->update(['plan_path' => $path]);
        }

        return redirect()->route('sites.index')
            ->with('success', 'Site créé avec succès.');
    }

    public function show(Site $site)
    {
        return Inertia::render('Sites/Show', [
            'site' => $site->load(['locations', 'stockItems.supply', 'stockItems.location'])
        ]);
    }

    public function edit(Site $site)
    {
        return Inertia::render('Sites/Edit', [
            'site' => $site
        ]);
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'plan' => 'nullable|image|max:5120',
            'is_headquarters' => 'boolean'
        ]);

        $site->update($validated);

        if ($request->hasFile('plan')) {
            // Supprimer l'ancien plan si existant
            if ($site->plan_path) {
                Storage::disk('public')->delete($site->plan_path);
            }
            $path = $request->file('plan')->store('plans', 'public');
            $site->update(['plan_path' => $path]);
        }

        return redirect()->route('sites.index')
            ->with('success', 'Site mis à jour avec succès.');
    }

    public function destroy(Site $site)
    {
        if ($site->plan_path) {
            Storage::disk('public')->delete($site->plan_path);
        }
        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Site supprimé avec succès.');
    }

    public function uploadPlan(Request $request, Site $site)
    {
        $request->validate([
            'plan' => 'required|image|max:5120'
        ]);

        if ($site->plan_path) {
            Storage::disk('public')->delete($site->plan_path);
        }

        $path = $request->file('plan')->store('plans', 'public');
        $site->update(['plan_path' => $path]);

        return redirect()->route('sites.show', $site)
            ->with('success', 'Plan mis à jour avec succès.');
    }

    public function apiIndex()
    {
        return Site::select('id', 'name', 'slug')
            ->orderBy('name')
            ->get();
    }

    public function apiShow(Site $site)
    {
        return $site->load(['locations', 'stockItems.supply', 'stockItems.location']);
    }

    public function removePlan(Site $site)
    {
        if ($site->plan_path) {
            Storage::disk('public')->delete($site->plan_path);
            $site->update(['plan_path' => null]);
        }

        return redirect()->route('sites.show', $site)
            ->with('success', 'Plan supprimé avec succès.');
    }
}
