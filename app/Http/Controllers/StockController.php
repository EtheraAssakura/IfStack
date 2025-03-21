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
      // Créer un fichier de log spécifique pour l'export
      $logFile = storage_path('logs/export.log');
      file_put_contents($logFile, "=== Début de l'export " . now()->format('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

      $site = $site ?? $request->input('site');
      file_put_contents($logFile, "Site: " . $site . "\n", FILE_APPEND);
      file_put_contents($logFile, "Request site: " . $request->input('site') . "\n", FILE_APPEND);
      file_put_contents($logFile, "Request all: " . json_encode($request->all()) . "\n", FILE_APPEND);
      file_put_contents($logFile, "Method: " . $request->method() . "\n", FILE_APPEND);

      // Construire la requête SQL
      $query = DB::table('stock_items')
        ->select([
          'stock_items.id',
          'stock_items.estimated_quantity',
          'stock_items.local_alert_threshold',
          'locations.name as emplacement_name',
          'supplies.name as fourniture_name',
          'supplies.reference as fourniture_reference'
      ])
        ->join('locations', 'locations.id', '=', 'stock_items.location_id')
        ->join('supplies', 'supplies.id', '=', 'stock_items.supply_id')
        ->join('sites', 'sites.id', '=', 'locations.site_id')
        ->where('sites.slug', 'isfac-' . $site)
        ->orderBy('locations.name')
        ->orderBy('supplies.name');

      file_put_contents($logFile, "SQL Query: " . $query->toSql() . "\n", FILE_APPEND);
      file_put_contents($logFile, "SQL Bindings: " . json_encode($query->getBindings()) . "\n", FILE_APPEND);

      $stocks = $query->get();
      file_put_contents($logFile, "Nombre de stocks: " . $stocks->count() . "\n", FILE_APPEND);
      file_put_contents($logFile, "Premier stock:\n" . json_encode($stocks->first(), JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
      file_put_contents($logFile, "Encodage du premier stock: " . mb_detect_encoding(json_encode($stocks->first())) . "\n", FILE_APPEND);

      // Créer le fichier Excel
      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();

      // En-têtes
      $sheet->setCellValue('A1', 'Emplacement');
      $sheet->setCellValue('B1', 'Fourniture');
      $sheet->setCellValue('C1', 'Référence');
      $sheet->setCellValue('D1', 'Quantité');
      $sheet->setCellValue('E1', 'Seuil d\'alerte');

      // Style des en-têtes
      $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
          'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
          'startColor' => ['rgb' => 'E2EFDA']
        ]
      ];
      $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

      // Données
      $row = 2;
      foreach ($stocks as $stock) {
        $sheet->setCellValue('A' . $row, $stock->emplacement_name);
        $sheet->setCellValue('B' . $row, $stock->fourniture_name);
        $sheet->setCellValue('C' . $row, $stock->fourniture_reference);
        $sheet->setCellValue('D' . $row, $stock->estimated_quantity);
        $sheet->setCellValue('E' . $row, $stock->local_alert_threshold);
        $row++;
      }

      // Ajuster la largeur des colonnes
      foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
      }

      // Créer le dossier temp s'il n'existe pas
      $tempDir = storage_path('app/temp');
      if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
      }

      // Sauvegarder le fichier
      $filename = 'stocks_' . ($site ? $site . '_' : '') . now()->format('Y-m-d_His') . '.xlsx';
      $filepath = $tempDir . '/' . $filename;
      $writer = new Xlsx($spreadsheet);
      $writer->save($filepath);

      file_put_contents($logFile, "Fichier temporaire créé: " . $filepath . "\n", FILE_APPEND);
      file_put_contents($logFile, "Le fichier existe: " . (file_exists($filepath) ? 'Oui' : 'Non') . "\n", FILE_APPEND);
      file_put_contents($logFile, "Taille du fichier: " . filesize($filepath) . " bytes\n", FILE_APPEND);

      // Forcer le téléchargement du fichier
      return response()->download($filepath, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0'
      ])->deleteFileAfterSend(true);
    } catch (\Exception $e) {
      file_put_contents($logFile, "Erreur: " . $e->getMessage() . "\n", FILE_APPEND);
      file_put_contents($logFile, "Trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
      file_put_contents($logFile, "=== Fin de l'export avec erreur " . now()->format('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
      throw $e;
    }
  }
}
