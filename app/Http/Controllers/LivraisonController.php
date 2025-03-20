<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Emplacement;
use App\Models\Delivery;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class LivraisonController extends Controller
{
  public function index()
  {
    $livraisons = Delivery::with(['commande', 'user', 'details.emplacement.etablissement'])
      ->orderBy('date_prevue')
      ->get();
    return view('livraisons.index', compact('livraisons'));
  }

  public function create(Order $commande)
  {
    $emplacements = Emplacement::with('etablissement')->get();
    return view('livraisons.create', compact('commande', 'emplacements'));
  }

  public function store(Request $request, Order $commande)
  {
    $validated = $request->validate([
      'date_prevue' => 'required|date',
      'details' => 'required|array|min:1',
      'details.*.emplacement_id' => 'required|exists:emplacements,id',
      'details.*.fourniture_id' => 'required|exists:fournitures,id',
      'details.*.quantite' => 'required|integer|min:1',
    ]);

    DB::transaction(function () use ($validated, $commande) {
      $livraison = Delivery::create([
        'commande_id' => $commande->id,
        'user_id' => Auth::id(),
        'date_prevue' => $validated['date_prevue'],
        'statut' => 'planifiee',
      ]);

      foreach ($validated['details'] as $detail) {
        $livraison->details()->create($detail);
      }
    });

    return redirect()->route('livraisons.index')
      ->with('success', 'Livraison planifiée avec succès.');
  }

  public function show(Delivery $livraison)
  {
    $livraison->load(['commande', 'user', 'details.emplacement.etablissement', 'details.fourniture']);
    return view('livraisons.show', compact('livraison'));
  }

  public function effectuer(Request $request, Delivery $livraison)
  {
    DB::transaction(function () use ($request, $livraison) {
      foreach ($livraison->details as $detail) {
        $stock = Stock::firstOrCreate(
          [
            'fourniture_id' => $detail->fourniture_id,
            'emplacement_id' => $detail->emplacement_id,
          ],
          ['quantite_estimee' => 0]
        );

        $stock->increment('quantite_estimee', $detail->quantite);
      }

      $livraison->update([
        'date_effective' => now(),
        'statut' => 'effectuee',
      ]);

      if ($livraison->commande->livraisons()->where('statut', '!=', 'effectuee')->doesntExist()) {
        $livraison->commande->update(['statut' => 'livree']);
      }
    });

    return redirect()->route('livraisons.show', $livraison)
      ->with('success', 'Livraison effectuée avec succès.');
  }

  public function generateBonLivraison(Delivery $livraison)
  {
    $livraison->load(['commande', 'user', 'details.emplacement.etablissement', 'details.fourniture']);

    $pdf = PDF::loadView('livraisons.bon', compact('livraison'));

    return $pdf->download('bon_livraison_' . $livraison->id . '.pdf');
  }

  public function calendar()
  {
    $livraisons = Delivery::with(['commande', 'user'])
      ->where('date_prevue', '>=', now()->subDays(30))
      ->get()
      ->map(function ($livraison) {
        return [
          'id' => $livraison->id,
          'title' => 'Livraison #' . $livraison->id,
          'start' => $livraison->date_prevue->format('Y-m-d'),
          'url' => route('livraisons.show', $livraison),
          'backgroundColor' => $this->getStatusColor($livraison->statut),
        ];
      });

    return view('livraisons.calendar', compact('livraisons'));
  }

  private function getStatusColor($status): string
  {
    return match ($status) {
      'planifiee' => '#3498db',
      'en_cours' => '#f1c40f',
      'effectuee' => '#2ecc71',
      default => '#95a5a6',
    };
  }
}
