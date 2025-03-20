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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

  public function create(Request $request): Response
  {
    $site = $request->get('site', '');

    Log::info('Création de stock', [
      'site' => $site,
      'request_all' => $request->all()
    ]);

    $query = Emplacement::with(['etablissement', 'stocks']);

    if ($site) {
      $query->whereHas('etablissement', function ($q) use ($site) {
        $q->where('slug', 'isfac-' . $site);
      });

      Log::info('Filtrage des emplacements par site', [
        'site' => $site,
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings()
      ]);
    }

    $emplacements = $query->get();
    $fournitures = Fourniture::all();

    Log::info('Emplacements récupérés', [
      'count' => $emplacements->count(),
      'site' => $site,
      'first_emplacement' => $emplacements->first() ? [
        'id' => $emplacements->first()->id,
        'name' => $emplacements->first()->name,
        'etablissement' => $emplacements->first()->etablissement->name,
      ] : null,
    ]);

    return Inertia::render('Stocks/Create', [
      'emplacements' => $emplacements->map(function ($emplacement) {
        return [
          'id' => $emplacement->id,
          'name' => $emplacement->name,
          'etablissement' => [
            'id' => $emplacement->etablissement->id,
            'name' => $emplacement->etablissement->name,
            'slug' => $emplacement->etablissement->slug,
          ],
          'stocks' => $emplacement->stocks->map(function ($stock) {
            return [
              'fourniture_id' => $stock->supply_id,
              'estimated_quantity' => $stock->estimated_quantity,
              'local_alert_threshold' => $stock->local_alert_threshold,
            ];
          }),
        ];
      }),
      'fournitures' => $fournitures,
      'site' => $site,
      'site_name' => $site ? str_replace('isfac-', '', $emplacements->first()?->etablissement->name ?? '') : '',
    ]);
  }

  public function store(Request $request)
  {
    Log::info('Vérification de la table supplies', [
      'table_exists' => Schema::hasTable('supplies'),
      'tables' => DB::select('SHOW TABLES'),
    ]);

    $validated = $request->validate([
      'emplacement_id' => 'required|exists:locations,id',
      'stocks' => 'required|array|min:1',
      'stocks.*.fourniture_id' => 'required|exists:supplies,id',
      'stocks.*.estimated_quantity' => 'required|integer|min:0',
      'stocks.*.local_alert_threshold' => 'required|integer|min:0',
    ]);

    // Récupérer les stocks existants pour cet emplacement
    $existingStocks = Stock::where('location_id', $validated['emplacement_id'])->get();

    // Créer un tableau des IDs des fournitures dans la requête
    $requestedSupplyIds = collect($validated['stocks'])->pluck('fourniture_id')->toArray();

    // Supprimer les stocks qui n'existent plus dans la requête
    $existingStocks->each(function ($stock) use ($requestedSupplyIds) {
      if (!in_array($stock->supply_id, $requestedSupplyIds)) {
        $stock->delete();
      }
    });

    // Mettre à jour ou créer les stocks
    foreach ($validated['stocks'] as $stockData) {
      Stock::updateOrCreate(
        [
          'location_id' => $validated['emplacement_id'],
          'supply_id' => $stockData['fourniture_id'],
        ],
        [
          'estimated_quantity' => $stockData['estimated_quantity'],
          'local_alert_threshold' => $stockData['local_alert_threshold'],
        ]
      );
    }

    // Récupérer le site de l'emplacement créé
    $emplacement = Emplacement::with('etablissement')->find($validated['emplacement_id']);
    $site = str_replace('isfac-', '', $emplacement->etablissement->slug);

    return redirect()->route('stocks.by-site', ['site' => $site])
      ->with('success', 'Stocks mis à jour avec succès.');
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

  public function export(Request $request, ?string $site = null)
  {
    try {
      Log::info('Début de l\'export des stocks', [
        'site' => $site,
        'request_site' => $request->query('site'),
        'request_all' => $request->all(),
        'method' => $request->method()
      ]);

      $site = $site ?? $request->query('site');

      // Récupérer les données
      $stocks = Stock::select(
        'stock_items.id',
        'stock_items.estimated_quantity',
        'stock_items.local_alert_threshold',
        'locations.name as emplacement_name',
        'supplies.name as fourniture_name',
        'supplies.reference as fourniture_reference'
      )
        ->join('locations', 'locations.id', '=', 'stock_items.location_id')
        ->join('supplies', 'supplies.id', '=', 'stock_items.supply_id')
        ->join('sites', 'sites.id', '=', 'locations.site_id')
        ->when($site, function ($query) use ($site) {
          return $query->where('sites.slug', 'isfac-' . $site);
        })
        ->orderBy('locations.name')
        ->orderBy('supplies.name')
        ->get();

      Log::info('Stocks récupérés pour l\'export', [
        'count' => $stocks->count(),
        'site' => $site,
        'first_stock' => $stocks->first()
      ]);

      // Créer le fichier Excel
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();

      // En-tête simple
      $sheet->setCellValue('A1', 'État des stocks');
      $sheet->setCellValue('A2', 'Date d\'export : ' . now()->format('d/m/Y H:i'));
      if ($site) {
        $sheet->setCellValue('A3', 'Site : ' . ucfirst($site));
      }

      // En-têtes des colonnes
      $headers = ['Emplacement', 'Fourniture', 'Référence', 'Quantité', 'Seuil Local', 'Statut'];
      $col = 'A';
      $row = 5;
      foreach ($headers as $header) {
        $sheet->setCellValue($col . $row, $header);
        $col++;
      }

      // Données
      $row = 6;
      foreach ($stocks as $stock) {
        $sheet->setCellValue('A' . $row, $stock->emplacement_name);
        $sheet->setCellValue('B' . $row, $stock->fourniture_name);
        $sheet->setCellValue('C' . $row, $stock->fourniture_reference);
        $sheet->setCellValue('D' . $row, $stock->estimated_quantity);
        $sheet->setCellValue('E' . $row, $stock->local_alert_threshold);

        $status = $stock->estimated_quantity <= $stock->local_alert_threshold ? 'En alerte' : 'Normal';
        $sheet->setCellValue('F' . $row, $status);

        $row++;
      }

      // Auto-dimensionner les colonnes
      foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
      }

      Log::info('Fichier Excel créé, début de l\'écriture');

      // Créer le fichier temporaire
      $tempFile = storage_path('app/temp/stocks_' . ($site ? $site . '_' : '') . now()->format('Y-m-d_His') . '.xlsx');

      // Créer le dossier temp s'il n'existe pas
      if (!file_exists(storage_path('app/temp'))) {
        mkdir(storage_path('app/temp'), 0777, true);
      }

      // Sauvegarder le fichier
      $writer = new Xlsx($spreadsheet);
      $writer->save($tempFile);

      Log::info('Fichier Excel sauvegardé temporairement', [
        'temp_file' => $tempFile,
        'file_exists' => file_exists($tempFile),
        'file_size' => filesize($tempFile)
      ]);

      // Nettoyer le buffer de sortie
      if (ob_get_level()) {
        ob_end_clean();
      }

      // Définir les en-têtes HTTP
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="stocks_' . ($site ? $site . '_' : '') . now()->format('Y-m-d_His') . '.xlsx"');
      header('Cache-Control: max-age=0');
      header('Pragma: no-cache');
      header('Expires: 0');
      header('Content-Length: ' . filesize($tempFile));
      header('Content-Transfer-Encoding: binary');

      // Lire et envoyer le fichier
      readfile($tempFile);

      // Supprimer le fichier temporaire
      unlink($tempFile);

      exit;
    } catch (\Exception $e) {
      Log::error('Erreur lors de l\'export des stocks : ' . $e->getMessage());
      Log::error($e->getTraceAsString());
      return back()->with('error', 'Une erreur est survenue lors de l\'exportation des stocks. Veuillez réessayer.');
    }
  }
}
