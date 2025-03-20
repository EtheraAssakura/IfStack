<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Fourniture;
use App\Models\Fournisseur;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Order;
use App\Models\Supply;
use App\Models\Supplier;

class CommandeController extends Controller
{
  public function index()
  {
    $commandes = Order::with(['fournisseur', 'items.fourniture', 'user'])
      ->orderBy('created_at', 'desc')
      ->get();
    return view('commandes.index', compact('commandes'));
  }

  public function create()
  {
    $fournitures = Supply::all();
    $fournisseurs = Supplier::all();
    return view('commandes.create', compact('fournitures', 'fournisseurs'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'fournisseur_id' => 'required|exists:suppliers,id',
      'date_commande' => 'required|date',
      'date_livraison_prevue' => 'required|date|after:date_commande',
      'items' => 'required|array|min:1',
      'items.*.fourniture_id' => 'required|exists:supplies,id',
      'items.*.quantite' => 'required|integer|min:1',
      'notes' => 'nullable|string'
    ]);

    DB::transaction(function () use ($validated) {
      $commande = Order::create([
        'fournisseur_id' => $validated['fournisseur_id'],
        'date_commande' => $validated['date_commande'],
        'date_livraison_prevue' => $validated['date_livraison_prevue'],
        'notes' => $validated['notes'],
        'user_id' => Auth::id()
      ]);

      foreach ($validated['items'] as $item) {
        $commande->items()->create([
          'fourniture_id' => $item['fourniture_id'],
          'quantite' => $item['quantite']
        ]);
      }
    });

    return redirect()->route('commandes.index')
      ->with('success', 'Commande créée avec succès.');
  }

  public function show(Order $commande)
  {
    $commande->load(['fournisseur', 'items.fourniture', 'user']);
    return view('commandes.show', compact('commande'));
  }

  public function edit(Order $commande)
  {
    $fournitures = Supply::all();
    $fournisseurs = Supplier::all();
    $commande->load('items');
    return view('commandes.edit', compact('commande', 'fournitures', 'fournisseurs'));
  }

  public function update(Request $request, Order $commande)
  {
    $validated = $request->validate([
      'fournisseur_id' => 'required|exists:suppliers,id',
      'date_commande' => 'required|date',
      'date_livraison_prevue' => 'required|date|after:date_commande',
      'items' => 'required|array|min:1',
      'items.*.fourniture_id' => 'required|exists:supplies,id',
      'items.*.quantite' => 'required|integer|min:1',
      'notes' => 'nullable|string'
    ]);

    DB::transaction(function () use ($validated, $commande) {
      $commande->update([
        'fournisseur_id' => $validated['fournisseur_id'],
        'date_commande' => $validated['date_commande'],
        'date_livraison_prevue' => $validated['date_livraison_prevue'],
        'notes' => $validated['notes']
      ]);

      $commande->items()->delete();

      foreach ($validated['items'] as $item) {
        $commande->items()->create([
          'fourniture_id' => $item['fourniture_id'],
          'quantite' => $item['quantite']
        ]);
      }
    });

    return redirect()->route('commandes.index')
      ->with('success', 'Commande mise à jour avec succès.');
  }

  public function destroy(Order $commande)
  {
    $commande->delete();
    return redirect()->route('commandes.index')
      ->with('success', 'Commande supprimée avec succès.');
  }

  public function exportExcel(Order $commande)
  {
    $commande->load(['fournisseur', 'items.fourniture', 'user']);
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // En-tête
    $sheet->setCellValue('A1', 'Bon de commande - ' . $commande->reference);
    $sheet->setCellValue('A3', 'Fournisseur: ' . $commande->fournisseur->nom);
    $sheet->setCellValue('A4', 'Date: ' . $commande->date_commande->format('d/m/Y'));

    // En-têtes des colonnes
    $sheet->setCellValue('A6', 'Référence');
    $sheet->setCellValue('B6', 'Désignation');
    $sheet->setCellValue('C6', 'Quantité');
    $sheet->setCellValue('D6', 'Prix unitaire');
    $sheet->setCellValue('E6', 'Total HT');

    // Données
    $row = 7;
    foreach ($commande->lignes as $ligne) {
      $sheet->setCellValue('A' . $row, $ligne->fourniture->reference_isfac);
      $sheet->setCellValue('B' . $row, $ligne->fourniture->nom);
      $sheet->setCellValue('C' . $row, $ligne->quantite);
      $sheet->setCellValue('D' . $row, $ligne->prix_unitaire);
      $sheet->setCellValue('E' . $row, '=C' . $row . '*D' . $row);
      $row++;
    }

    // Total
    $sheet->setCellValue('D' . ($row + 1), 'Total HT:');
    $sheet->setCellValue('E' . ($row + 1), '=SUM(E7:E' . ($row - 1) . ')');

    // Style
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A6:E6')->getFont()->setBold(true);
    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->getStyle('D:E')->getNumberFormat()->setFormatCode('#,##0.00 €');

    // Création du fichier
    $writer = new Xlsx($spreadsheet);
    $filename = 'commande_' . $commande->reference . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
  }

  public function exportPdf(Order $commande)
  {
    $commande->load(['fournisseur', 'items.fourniture', 'user']);
    // ... rest of the method ...
  }

  public function exportBonCommande(Order $commande)
  {
    $commande->load(['fournisseur', 'items.fourniture', 'user']);
    // ... rest of the method ...
  }

  public function valider(Order $commande)
  {
    $commande->update(['statut' => 'validee']);
    return redirect()->route('commandes.index')
      ->with('success', 'Commande validée avec succès.');
  }

  public function annuler(Order $commande)
  {
    $commande->update(['statut' => 'annulee']);
    return redirect()->route('commandes.index')
      ->with('success', 'Commande annulée avec succès.');
  }
}
