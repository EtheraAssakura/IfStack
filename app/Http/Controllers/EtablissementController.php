<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'plan' => 'nullable|image|max:5120' // 5MB max
        ]);

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
        $etablissement->load(['emplacements.stocks', 'stocks']);
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
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255'
        ]);

        $etablissement->update($validated);

        return redirect()->route('etablissements.index')
            ->with('success', 'Établissement mis à jour avec succès.');
    }

    public function destroy(Etablissement $etablissement)
    {
        if ($etablissement->plan_path) {
            Storage::disk('public')->delete($etablissement->plan_path);
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
}
