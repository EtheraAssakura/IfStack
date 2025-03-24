<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class EtablissementController extends Controller
{
    public function index()
    {
        $etablissements = Etablissement::withCount(['emplacements', 'stocks'])->get();
        return Inertia::render('Etablissements/Index', [
            'etablissements' => $etablissements
        ]);
    }

    public function create()
    {
        return Inertia::render('Etablissements/Create');
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
            'plan' => 'nullable|image|max:5120' // 5MB max
        ]);

        // Générer le slug à partir du nom
        $validated['slug'] = 'isfac-' . strtolower(str_replace(' ', '-', $validated['name']));

        $etablissement = Etablissement::create($validated);

        if ($request->hasFile('plan')) {
            $path = $request->file('plan')->store('plans', 'public');
            $etablissement->update(['plan_path' => $path]);
        }

        return redirect()->route('etablissements.index')
            ->with('success', 'Établissement créé avec succès.');
    }

    public function show(Etablissement $etablissement)
    {
        $etablissement->load(['emplacements.stocks.fourniture', 'stocks.fourniture']);
        return Inertia::render('Etablissements/Show', [
            'etablissement' => $etablissement
        ]);
    }

    public function edit(Etablissement $etablissement)
    {
        return Inertia::render('Etablissements/Edit', [
            'etablissement' => $etablissement
        ]);
    }

    public function update(Request $request, Etablissement $etablissement)
    {
        Log::info('Début de la mise à jour de l\'établissement', [
            'etablissement_id' => $etablissement->id,
            'request_data' => $request->all(),
            'has_plan' => $request->hasFile('plan'),
            'files' => $request->allFiles(),
            'headers' => $request->headers->all(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method()
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'city' => 'required|string|max:255',
                'postal_code' => 'required|string|max:10',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'plan' => 'nullable|image|max:5120' // 5MB max
            ]);

            Log::info('Validation réussie', ['validated_data' => $validated]);

            if ($request->hasFile('plan')) {
                Log::info('Traitement du nouveau plan', [
                    'file' => $request->file('plan'),
                    'original_name' => $request->file('plan')->getClientOriginalName(),
                    'mime_type' => $request->file('plan')->getMimeType(),
                    'size' => $request->file('plan')->getSize()
                ]);

                if ($etablissement->plan_path) {
                    $oldPath = str_replace('/storage/', 'public/', $etablissement->plan_path);
                    Log::info('Suppression de l\'ancien plan', ['path' => $oldPath]);
                    Storage::delete($oldPath);
                }

                $path = $request->file('plan')->store('plans', 'public');
                $validated['plan_path'] = $path;
                Log::info('Nouveau plan stocké', ['path' => $path]);
            }

            $etablissement->update($validated);

            Log::info('Mise à jour réussie');

            return redirect()->route('etablissements.show', $etablissement->id)
                ->with('success', 'Établissement mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'validation_errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : null
            ]);

            return back()->withErrors($e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : ['error' => 'Une erreur est survenue lors de la mise à jour.']);
        }
    }

    public function destroy(Etablissement $etablissement)
    {
        if ($etablissement->plan_path) {
            Storage::delete(str_replace('/storage/', 'public/', $etablissement->plan_path));
        }

        $etablissement->delete();

        return redirect()->route('etablissements.index')
            ->with('success', 'Établissement supprimé avec succès.');
    }

    public function uploadPlan(Request $request, Etablissement $etablissement)
    {
        $request->validate([
            'plan' => 'required|image|max:5120' // 5MB max
        ]);

        if ($etablissement->plan_path) {
            Storage::disk('public')->delete($etablissement->plan_path);
        }

        $path = $request->file('plan')->store('plans', 'public');
        $etablissement->update(['plan_path' => $path]);

        return redirect()->route('etablissements.show', $etablissement)
            ->with('success', 'Plan mis à jour avec succès.');
    }

    public function apiIndex()
    {
        $sites = Etablissement::select('id', 'name')
            ->with(['locations' => function ($query) {
                $query->select('id', 'name', 'etablissement_id');
            }])
            ->get();

        return response()->json($sites);
    }

    public function apiShow(Etablissement $site)
    {
        $site->load(['locations' => function ($query) {
            $query->select('id', 'name', 'etablissement_id');
        }]);

        return response()->json($site);
    }

    public function removePlan(Etablissement $etablissement)
    {
        if ($etablissement->plan_path) {
            Storage::delete(str_replace('/storage/', 'public/', $etablissement->plan_path));
            $etablissement->update(['plan_path' => null]);
        }

        return redirect()->route('etablissements.show', $etablissement)
            ->with('success', 'Plan supprimé avec succès.');
    }
}
