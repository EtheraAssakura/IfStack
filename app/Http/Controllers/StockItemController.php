<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Location;
use App\Models\Site;
use App\Models\Supply;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockItemController extends Controller
{
    public function sites(): Response
    {
        $sites = Site::select('id', 'name', 'slug')
            ->orderBy('id', 'asc')
            ->orderBy('name')
            ->get()
            ->map(function ($site) {
                return [
                    'name' => $site->name,
                    'route' => 'stock-items.by-site',
                    'params' => ['site' => str_replace('isfac-', '', $site->slug)]
                ];
            });

        return Inertia::render('StockItems/Sites', [
            'sites' => $sites
        ]);
    }

    public function index(Request $request): Response
    {
        return $this->sites();
    }

    public function create(Request $request): Response
    {
        $site = $request->get('site', '');
        Log::info('Site parameter:', ['site' => $site]);

        $query = Location::with(['site', 'stockItems']);
        Log::info('Initial query built');

        if ($site) {
            $query->whereHas('site', function ($q) use ($site) {
                $q->where('slug', $site);
            });
            Log::info('Site filter applied');
        }

        // Log de la requête SQL
        Log::info('SQL Query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        $locations = $query->get();
        Log::info('Locations retrieved:', ['count' => $locations->count()]);

        $supplies = Supply::all();
        Log::info('Supplies retrieved:', ['count' => $supplies->count()]);

        return Inertia::render('StockItems/Create', [
            'locations' => $locations->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'site' => [
                        'id' => $location->site->id,
                        'name' => $location->site->name,
                        'slug' => $location->site->slug,
                    ],
                    'stocks' => $location->stockItems->map(function ($stock) {
                        return [
                            'supply_id' => $stock->supply_id,
                            'estimated_quantity' => $stock->estimated_quantity,
                            'local_alert_threshold' => $stock->local_alert_threshold,
                        ];
                    }),
                ];
            }),
            'supplies' => $supplies,
            'site' => $site,
            'site_name' => $site ? str_replace('isfac-', '', $locations->first()?->site->name ?? '') : '',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'stocks' => 'required|array|min:1',
            'stocks.*.supply_id' => 'required|exists:supplies,id',
            'stocks.*.estimated_quantity' => 'required|integer|min:0',
            'stocks.*.local_alert_threshold' => 'required|integer|min:0'
        ]);

        $location = Location::findOrFail($validated['location_id']);
        Log::info('Stock update started', ['location_id' => $validated['location_id']]);

        // Récupérer tous les stocks existants pour cet emplacement
        $existingStocks = StockItem::where('location_id', $validated['location_id'])
            ->get()
            ->keyBy(function ($stock) {
                return (string) $stock->supply_id;
            });

        Log::info('Existing stocks found', [
            'count' => $existingStocks->count(),
            'stocks' => $existingStocks->toArray()
        ]);

        // Créer un tableau des supply_ids de la requête
        $requestedSupplyIds = collect($validated['stocks'])->pluck('supply_id')->toArray();

        // Supprimer les stocks qui ne sont plus dans la liste
        $stocksToDelete = $existingStocks->filter(function ($stock) use ($requestedSupplyIds) {
            return !in_array((string) $stock->supply_id, $requestedSupplyIds);
        });

        foreach ($stocksToDelete as $stock) {
            Log::info('Deleting stock', [
                'stock_id' => $stock->id,
                'supply_id' => $stock->supply_id
            ]);

            // Supprimer d'abord les mouvements de stock associés
            StockMovement::where('stock_item_id', $stock->id)->delete();

            // Puis supprimer le stock
            $stock->delete();
        }

        foreach ($validated['stocks'] as $stock) {
            Log::info('Processing stock', [
                'supply_id' => $stock['supply_id'],
                'location_id' => $validated['location_id'],
                'new_quantity' => $stock['estimated_quantity']
            ]);

            // Vérifier si le stock existe déjà
            if ($existingStocks->has($stock['supply_id'])) {
                $existingStock = $existingStocks[$stock['supply_id']];
                Log::info('Found existing stock', [
                    'stock_id' => $existingStock->id,
                    'old_quantity' => $existingStock->estimated_quantity,
                    'new_quantity' => $stock['estimated_quantity']
                ]);

                // Mettre à jour le stock existant
                DB::table('stock_items')
                    ->where('id', $existingStock->id)
                    ->update([
                        'estimated_quantity' => $stock['estimated_quantity'],
                        'local_alert_threshold' => $stock['local_alert_threshold'],
                        'updated_at' => now()
                    ]);

                Log::info('Stock updated in database', [
                    'stock_id' => $existingStock->id,
                    'new_quantity' => $stock['estimated_quantity']
                ]);

                // Créer un mouvement de stock pour la mise à jour
                StockMovement::create([
                    'stock_item_id' => $existingStock->id,
                    'user_id' => Auth::id(),
                    'type' => 'update',
                    'quantity' => $stock['estimated_quantity'],
                    'comment' => 'Mise à jour du stock'
                ]);
            } else {
                Log::info('Creating new stock', [
                    'supply_id' => $stock['supply_id'],
                    'location_id' => $validated['location_id']
                ]);

                // Créer un nouveau stock
                $stockItem = StockItem::create([
                    'supply_id' => $stock['supply_id'],
                    'location_id' => $validated['location_id'],
                    'estimated_quantity' => $stock['estimated_quantity'],
                    'local_alert_threshold' => $stock['local_alert_threshold']
                ]);

                // Créer un mouvement de stock initial
                StockMovement::create([
                    'stock_item_id' => $stockItem->id,
                    'user_id' => Auth::id(),
                    'type' => 'initial',
                    'quantity' => $stock['estimated_quantity'],
                    'comment' => 'Stock initial'
                ]);
            }
        }

        // Vérifier les stocks après la mise à jour
        $updatedStocks = StockItem::where('location_id', $validated['location_id'])->get();
        Log::info('Final stock state', [
            'stocks' => $updatedStocks->toArray()
        ]);

        Log::info('Stock update completed', ['location_id' => $validated['location_id']]);

        return redirect()->route('stock-items.by-site', ['site' => $location->site->slug])
            ->with('success', 'Stocks mis à jour avec succès.');
    }

    public function stocksBySite(Request $request): Response
    {
        $site = $request->get('site', '');
        $locationId = $request->get('location');

        // Vérifier si le slug commence déjà par 'isfac-'
        $siteSlug = str_starts_with($site, 'isfac-') ? $site : 'isfac-' . $site;
        $site = Site::where('slug', $siteSlug)->first();

        if (!$site) {
            abort(404);
        }

        $query = StockItem::with(['supply', 'location'])
            ->whereHas('location', function ($query) use ($site) {
                $query->where('site_id', $site->id);
            });

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        $stockItems = $query->get()
            ->map(function ($stockItem) {
                return [
                    'id' => $stockItem->id,
                    'supply' => [
                        'id' => $stockItem->supply->id,
                        'name' => $stockItem->supply->name,
                        'reference' => $stockItem->supply->reference
                    ],
                    'location' => [
                        'id' => $stockItem->location->id,
                        'name' => $stockItem->location->name,
                        'site' => [
                            'id' => $stockItem->location->site->id,
                            'name' => $stockItem->location->site->name
                        ]
                    ],
                    'estimated_quantity' => $stockItem->estimated_quantity,
                    'local_alert_threshold' => $stockItem->local_alert_threshold,
                    'last_update' => $stockItem->updated_at->format('d/m/Y H:i')
                ];
            });

        // Grouper les stocks par établissement
        $groupedStocks = $stockItems->groupBy(function ($stock) {
            return $stock['location']['site']['name'];
        })->toArray();

        return Inertia::render('StockItems/Index', [
            'site' => $site,
            'stocks' => $groupedStocks,
            'location' => $locationId ? (int) $locationId : null
        ]);
    }

    public function show(StockItem $stockItem): Response
    {
        // Charger les relations avec eager loading
        $stockItem->load(['supply', 'location.site', 'alerts']);

        return Inertia::render('StockItems/Show', [
            'stockItem' => [
                'id' => $stockItem->id,
                'estimated_quantity' => $stockItem->estimated_quantity,
                'local_alert_threshold' => $stockItem->local_alert_threshold,
                'supply' => [
                    'id' => $stockItem->supply->id,
                    'name' => $stockItem->supply->name,
                    'reference' => $stockItem->supply->reference,
                    'packaging' => $stockItem->supply->packaging,
                ],
                'location' => [
                    'id' => $stockItem->location->id,
                    'name' => $stockItem->location->name,
                    'site' => [
                        'id' => $stockItem->location->site->id,
                        'name' => $stockItem->location->site->name,
                    ],
                ],
                'alerts' => $stockItem->alerts->map(function ($alert) {
                    return [
                        'id' => $alert->id,
                        'type' => $alert->type === 'rupture' ? 'rupture' : 'seuil_atteint',
                        'message' => $alert->comment,
                        'created_at' => $alert->created_at,
                    ];
                }),
            ],
        ]);
    }

    public function edit(StockItem $stockItem): Response
    {
        return Inertia::render('StockItems/Edit', [
            'stockItem' => $stockItem->load(['supply', 'location.site'])
        ]);
    }

    public function update(Request $request, StockItem $stockItem)
    {

        $isUser = Auth::user()->roles->contains('name', 'Utilisateur');

        if ($isUser) {
            // Pour les utilisateurs, on ne permet que la mise à jour de la quantité
            $validated = $request->validate([
                'estimated_quantity' => 'required|numeric|min:0',
            ]);

            $previousQuantity = $stockItem->estimated_quantity;
            $stockItem->update($validated);

            // Vérifier si la quantité est passée sous le seuil d'alerte
            if ($stockItem->estimated_quantity <= $stockItem->local_alert_threshold && $previousQuantity > $stockItem->local_alert_threshold) {
                Alert::create([
                    'stock_item_id' => $stockItem->id,
                    'user_id' => Auth::id(),
                    'type' => 'seuil_atteint',
                    'title' => 'Alerte Stock - ' . $stockItem->supply->name,
                    'comment' => "Le stock de {$stockItem->supply->name} situé à l'emplacement {$stockItem->location->name} à {$stockItem->location->site->name} est en alerte (quantité : {$stockItem->estimated_quantity} seuil d'alerte : {$stockItem->local_alert_threshold})",
                ]);
            }

            return redirect()->back()->with('success', 'Quantité mise à jour avec succès.');
        }

        // Pour les autres rôles, on permet la mise à jour complète
        $validated = $request->validate([
            'estimated_quantity' => 'required|numeric|min:0',
            'local_alert_threshold' => 'required|numeric|min:0',
        ]);

        $previousQuantity = $stockItem->estimated_quantity;
        $stockItem->update($validated);

        // Vérifier si la quantité est passée sous le seuil d'alerte
        if ($stockItem->estimated_quantity <= $stockItem->local_alert_threshold && $previousQuantity > $stockItem->local_alert_threshold) {
            Alert::create([
                'stock_item_id' => $stockItem->id,
                'user_id' => Auth::id(),
                'type' => 'seuil_atteint',
                'title' => 'Alerte Stock - ' . $stockItem->supply->name,
                'comment' => "Le stock de {$stockItem->supply->name} situé à l'emplacement {$stockItem->location->name} à {$stockItem->location->site->name} est en alerte (quantité : {$stockItem->estimated_quantity} seuil d'alerte : {$stockItem->local_alert_threshold})",
            ]);
        }

        return redirect()->back()->with('success', 'Stock mis à jour avec succès.');
    }


    public function scanQr($qrCode): Response
    {
        $stockItem = StockItem::whereHas('location', function ($query) use ($qrCode) {
            $query->where('qr_code', $qrCode);
        })->first();

        if (!$stockItem) {
            abort(404);
        }

        return Inertia::render('StockItems/Show', [
            'stockItem' => $stockItem->load(['supply', 'location.site', 'stockMovements.user'])
        ]);
    }

    public function signalRupture(StockItem $stockItem)
    {
        // Charger les relations nécessaires
        $stockItem->load(['supply', 'location.site']);

        // Mettre à jour la quantité estimée à 0
        $stockItem->update([
            'estimated_quantity' => 0
        ]);

        Alert::create([
            'stock_item_id' => $stockItem->id,
            'user_id' => Auth::id(),
            'type' => 'rupture',
            'title' => 'Alerte Rupture - ' . $stockItem->supply->name,
            'comment' => "Le stock de {$stockItem->supply->name} situé à l'emplacement {$stockItem->location->name} à {$stockItem->location->site->name} est en rupture de stock",
        ]);

        return redirect()->back()->with('success', 'Rupture de stock signalée avec succès.');
    }

    public function export(Request $request, ?string $site = null)
    {
        $query = StockItem::with(['supply', 'location.site']);

        if ($site) {
            $site = Site::where('slug', 'isfac-' . $site)->first();
            if ($site) {
                $query->whereHas('location', function ($q) use ($site) {
                    $q->where('site_id', $site->id);
                });
            }
        }

        $stockItems = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $sheet->setCellValue('A1', 'Référence');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Emplacement');
        $sheet->setCellValue('D1', 'Site');
        $sheet->setCellValue('E1', 'Quantité estimée');
        $sheet->setCellValue('F1', 'Seuil d\'alerte');

        // Données
        $row = 2;
        foreach ($stockItems as $stockItem) {
            $sheet->setCellValue('A' . $row, $stockItem->supply->reference);
            $sheet->setCellValue('B' . $row, $stockItem->supply->name);
            $sheet->setCellValue('C' . $row, $stockItem->location->name);
            $sheet->setCellValue('D' . $row, $stockItem->location->site->name);
            $sheet->setCellValue('E' . $row, $stockItem->estimated_quantity);
            $sheet->setCellValue('F' . $row, $stockItem->local_alert_threshold);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'stock_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function take(Request $request): Response
    {
        $locationId = (int) $request->get('locationId');

        Log::info('Prise de stock - Paramètres reçus', [
            'locationId' => $locationId,
            'request_all' => $request->all()
        ]);

        $query = StockItem::with(['supply', 'location.site']);

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        $stocks = $query->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'supply' => [
                        'id' => $stock->supply->id,
                        'name' => $stock->supply->name,
                        'reference' => $stock->supply->reference,
                        'packaging' => $stock->supply->packaging,
                    ],
                    'location' => [
                        'id' => (int) $stock->location->id,
                        'name' => $stock->location->name,
                        'site' => [
                            'id' => (int) $stock->location->site->id,
                            'name' => $stock->location->site->name,
                        ],
                    ],
                    'estimated_quantity' => (int) $stock->estimated_quantity,
                    'local_alert_threshold' => (int) $stock->local_alert_threshold,
                ];
            });

        return Inertia::render('StockItems/Take', [
            'stocks' => $stocks,
            'locationId' => (string) $locationId,
        ]);
    }

    public function takeStock(Request $request, StockItem $stockItem)
    {
        Log::info('Requête de mise à jour de stock reçue', [
            'stock_id' => $stockItem->id,
            'request_data' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->url()
        ]);

        // Charger les relations nécessaires
        $stockItem->load(['supply', 'location.site']);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        Log::info('Données validées', ['validated' => $validated]);

        $previousQuantity = $stockItem->estimated_quantity;

        // Mettre à jour la quantité
        $stockItem->update([
            'estimated_quantity' => $validated['quantity']
        ]);

        Log::info('Stock mis à jour', [
            'stock_id' => $stockItem->id,
            'previous_quantity' => $previousQuantity,
            'new_quantity' => $stockItem->estimated_quantity
        ]);

        // Vérifier si la quantité est passée sous le seuil d'alerte
        if ($stockItem->estimated_quantity <= $stockItem->local_alert_threshold && $previousQuantity > $stockItem->local_alert_threshold) {
            Alert::create([
                'stock_item_id' => $stockItem->id,
                'user_id' => Auth::id(),
                'type' => 'seuil_atteint',
                'title' => 'Alerte Stock - ' . $stockItem->supply->name,
                'comment' => "Le stock de {$stockItem->supply->name} situé à l'emplacement {$stockItem->location->name} à {$stockItem->location->site->name} est en alerte (quantité : {$stockItem->estimated_quantity} seuil d'alerte : {$stockItem->local_alert_threshold})",
            ]);
        }

        return redirect()->back()->with('success', 'Inventaire mis à jour avec succès.');
    }
}
