<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Emplacement;
use App\Models\Fourniture;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
  public function sites(): Response
  {
    $sites = \App\Models\Etablissement::select('id', 'name', 'slug')
      ->orderBy('id', 'asc')
      ->orderBy('name')
      ->get()
      ->map(function ($site) {
        return [
          'name' => $site->name,
          'route' => 'stocks.by-site',
          'params' => ['site' => str_replace('isfac-', '', $site->slug)]
        ];
      });

    return Inertia::render('Stocks/Sites', [
      'sites' => $sites
    ]);
  }

  public function index(Request $request): Response
  {
    // Utiliser la méthode sites pour récupérer les sites
    return $this->sites();
  }

  public function stocksBySite(Request $request): Response
  {
    $query = Stock::with(['fourniture', 'emplacement.etablissement']);

    if ($request->has('site')) {
      $site = $request->get('site');
      Log::info('Filtrage par site', [
        'site' => $site,
        'request_all' => $request->all()
      ]);

      $query->whereHas('emplacement.etablissement', function ($q) use ($site) {
        $q->where('slug', 'isfac-' . $site);
      });

      // Ajout d'un log pour voir la requête SQL générée
      Log::info('Requête SQL', [
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings()
      ]);
    }

    $stocks = $query->get();

    Log::info('Stocks récupérés', [
      'count' => $stocks->count(),
      'site' => $request->get('site'),
      'first_stock' => $stocks->first() ? [
        'id' => $stocks->first()->id,
        'site' => $stocks->first()->emplacement?->etablissement?->slug,
        'name' => $stocks->first()->emplacement?->etablissement?->name,
        'emplacement' => $stocks->first()->emplacement?->name,
        'fourniture' => $stocks->first()->fourniture?->name
      ] : null,
      'all_stocks' => $stocks->map(function ($stock) {
        return [
          'id' => $stock->id,
          'site' => $stock->emplacement?->etablissement?->slug,
          'name' => $stock->emplacement?->etablissement?->name
        ];
      })
    ]);

    $groupedStocks = $stocks->groupBy(function ($stock) {
      return $stock->emplacement?->etablissement?->name ?? 'Non assigné';
    });

    Log::info('Stocks groupés', [
      'groups' => $groupedStocks->keys(),
      'site' => $request->get('site')
    ]);

    return Inertia::render('Stocks/Index', [
      'stocks' => $groupedStocks,
      'site' => $request->get('site', ''),
    ]);
  }

  public function show(Stock $stock): Response
  {
    $stock->load(['fourniture', 'emplacement.etablissement', 'alertes.user']);
    return Inertia::render('Stocks/Show', [
      'stock' => [
        'id' => $stock->id,
        'estimated_quantity' => $stock->estimated_quantity,
        'local_alert_threshold' => $stock->local_alert_threshold,
        'fourniture' => [
          'id' => $stock->fourniture->id,
          'name' => $stock->fourniture->name,
          'reference' => $stock->fourniture->reference,
        ],
        'emplacement' => [
          'id' => $stock->emplacement->id,
          'name' => $stock->emplacement->name,
          'etablissement' => [
            'id' => $stock->emplacement->etablissement->id,
            'name' => $stock->emplacement->etablissement->name,
          ],
        ],
        'alertes' => $stock->alertes->map(function ($alerte) {
          return [
            'id' => $alerte->id,
            'type' => $alerte->type,
            'comment' => $alerte->comment,
            'created_at' => $alerte->created_at,
            'user' => [
              'name' => $alerte->user->name,
            ],
          ];
        }),
      ],
    ]);
  }

  public function edit(Stock $stock): Response
  {
    $fournitures = Fourniture::all();
    $emplacements = Emplacement::with('etablissement')->get();
    return Inertia::render('Stocks/Edit', [
      'stock' => $stock,
      'fournitures' => $fournitures,
      'emplacements' => $emplacements,
    ]);
  }

  public function update(Request $request, Stock $stock)
  {
    $validated = $request->validate([
      'estimated_quantity' => 'required|integer|min:0',
      'local_alert_threshold' => 'nullable|integer|min:0',
    ]);

    $stock->update($validated);

    if ($stock->estEnRupture()) {
      Alerte::create([
        'stock_id' => $stock->id,
        'user_id' => Auth::id(),
        'type' => 'seuil_atteint',
        'commentaire' => 'Mise à jour du stock en dessous du seuil d\'alerte',
      ]);
    }

    return redirect()->route('stocks.show', $stock)
      ->with('success', 'Stock mis à jour avec succès.');
  }

  public function scanQr($qrCode): Response
  {
    $emplacement = Emplacement::where('qr_code', $qrCode)
      ->with(['stocks.fourniture', 'etablissement'])
      ->firstOrFail();

    return Inertia::render('Stocks/Scan', [
      'emplacement' => $emplacement,
    ]);
  }

  public function signalRupture(Request $request, Stock $stock)
  {
    $validated = $request->validate([
      'commentaire' => 'nullable|string|max:1000',
    ]);

    Alerte::create([
      'stock_id' => $stock->id,
      'user_id' => Auth::id(),
      'type' => 'rupture',
      'commentaire' => $validated['commentaire'] ?? 'Rupture de stock signalée',
    ]);

    return redirect()->back()
      ->with('success', 'Alerte de rupture signalée avec succès.');
  }

  public function export()
  {
    $stocks = Stock::with(['fourniture', 'emplacement.etablissement'])
      ->get()
      ->map(function ($stock) {
        return [
          'Établissement' => $stock->emplacement->etablissement->nom,
          'Emplacement' => $stock->emplacement->nom,
          'Fourniture' => $stock->fourniture->nom,
          'Référence' => $stock->fourniture->reference_isfac,
          'Quantité' => $stock->quantite_estimee,
        'Seuil local' => $stock->seuil_alerte_local,
          'Statut' => $stock->estEnRupture() ? 'En alerte' : 'Normal',
        ];
      });

    return response()->streamDownload(function () use ($stocks) {
      $f = fopen('php://output', 'w');
      fputcsv($f, array_keys($stocks->first()));
      foreach ($stocks as $stock) {
        fputcsv($f, $stock);
      }
      fclose($f);
    }, 'stocks.csv');
  }
}
