<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Emplacement;
use App\Models\Fourniture;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
  public function index()
  {
    $stocks = Stock::with(['fourniture', 'emplacement.etablissement'])
      ->get()
      ->groupBy('emplacement.etablissement.nom');

    return view('stocks.index', compact('stocks'));
  }

  public function show(Stock $stock)
  {
    $stock->load(['fourniture', 'emplacement.etablissement', 'alertes']);
    return view('stocks.show', compact('stock'));
  }

  public function edit(Stock $stock)
  {
    $fournitures = Fourniture::all();
    $emplacements = Emplacement::with('etablissement')->get();
    return view('stocks.edit', compact('stock', 'fournitures', 'emplacements'));
  }

  public function update(Request $request, Stock $stock)
  {
    $validated = $request->validate([
      'quantite_estimee' => 'required|integer|min:0',
      'seuil_alerte_local' => 'nullable|integer|min:0',
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

  public function scanQr($qrCode)
  {
    $emplacement = Emplacement::where('qr_code', $qrCode)
      ->with(['stocks.fourniture', 'etablissement'])
      ->firstOrFail();

    return view('stocks.scan', compact('emplacement'));
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
          'Seuil global' => $stock->fourniture->seuil_alerte_global,
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
