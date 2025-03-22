<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Stock;
use App\Models\Commande;
use App\Models\Fourniture;
use Carbon\Carbon;

class RapportController extends Controller
{
    public function consommation()
    {
        $consommation = Stock::with(['fourniture', 'location'])
            ->where('quantite', '<=', 10)
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'fourniture' => $stock->fourniture->nom,
                    'quantite' => $stock->quantite,
                    'location' => $stock->location->nom,
                    'date_derniere_commande' => $stock->date_derniere_commande,
                ];
            });

        return Inertia::render('Rapports/Consommation', [
            'consommation' => $consommation
        ]);
    }

    public function topProduits()
    {
        $topProduits = Fourniture::withCount(['commandes' => function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subMonths(3));
        }])
            ->orderByDesc('commandes_count')
            ->take(10)
            ->get()
            ->map(function ($fourniture) {
                return [
                    'id' => $fourniture->id,
                    'nom' => $fourniture->nom,
                    'nombre_commandes' => $fourniture->commandes_count,
                ];
            });

        return Inertia::render('Rapports/TopProduits', [
            'topProduits' => $topProduits
        ]);
    }

    public function alertes()
    {
        $alertes = Stock::with(['fourniture', 'location'])
            ->where('quantite', '<=', 5)
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'fourniture' => $stock->fourniture->nom,
                    'quantite' => $stock->quantite,
                    'location' => $stock->location->nom,
                    'seuil_alerte' => $stock->seuil_alerte,
                ];
            });

        return Inertia::render('Rapports/Alertes', [
            'alertes' => $alertes
        ]);
    }
}
