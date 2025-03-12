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

class CommandeController extends Controller
{
  public function index()
  {
    $commandes = Commande::with(['fournisseur', 'user', 'lignes.fourniture'])
      ->orderBy('created_at', 'desc')
      ->get();
    return view('commandes.index', compact('commandes'));
  }

  public function create()
  {
    $fournisseurs = Fournisseur::all();
    $fournitures = Fourniture::with(['stocks' => function ($query) {
      $query->whereRaw('quantite_estimee <= COALESCE(seuil_alerte_local, fournitures.seuil_alerte_global)');
    }])->get();

    return view('commandes.create', compact('fournisseurs', 'fournitures'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'fournisseur_id' => 'required|exists:fournisseurs,id',
      'date_commande' => 'required|date',
      'date_livraison_prevue' => 'nullable|date|after:date_commande',
      'lignes' => 'required|array|min:1',
      'lignes.*.fourniture_id' => 'required|exists:fournitures,id',
      'lignes.*.quantite' => 'required|integer|min:1',
      'lignes.*.prix_unitaire' => 'required|numeric|min:0',
    ]);

    DB::transaction(function () use ($validated) {
      $commande = Commande::create([
        'reference' => 'CMD' . date('YmdHis'),
        'fournisseur_id' => $validated['fournisseur_id'],
        'user_id' => Auth::id(),
        'statut' => 'en_cours',
        'date_commande' => $validated['date_commande'],
        'date_livraison_prevue' => $validated['date_livraison_prevue'],
      ]);

      foreach ($validated['lignes'] as $ligne) {
        $commande->lignes()->create($ligne);
      }
    });

    return redirect()->route('commandes.index')
      ->with('success', 'Commande créée avec succès.');
  }

  public function show(Commande $commande)
  {
    $commande->load(['fournisseur', 'user', 'lignes.fourniture', 'livraisons.details']);
    return view('commandes.show', compact('commande'));
  }

  public function exportExcel(Commande $commande)
  {
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

  public function valider(Commande $commande)
  {
    $commande->update(['statut' => 'validee']);
    return redirect()->route('commandes.show', $commande)
      ->with('success', 'Commande validée avec succès.');
  }

  public function annuler(Commande $commande)
  {
    $commande->update(['statut' => 'annulee']);
    return redirect()->route('commandes.show', $commande)
      ->with('success', 'Commande annulée avec succès.');
  }
}
