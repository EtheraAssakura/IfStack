<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Supply;
use App\Models\Supplier;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Inertia\Response;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['supplier', 'items.supply.suppliers' => function ($query) {
            $query->select('suppliers.id', 'suppliers.name')
                ->withPivot('supplier_reference');
        }, 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::select('id', 'name')->get();
        $supplies = Supply::select('id', 'name', 'reference', 'packaging')
            ->with(['suppliers' => function ($query) {
                $query->select('suppliers.id', 'suppliers.name')
                    ->withPivot('unit_price');
        }, 'stockItems' => function ($query) {
            $query->select('supply_id', 'local_alert_threshold', 'estimated_quantity');
            }])
            ->get()
            ->map(function ($supply) {
                return [
                    'id' => $supply->id,
                    'name' => $supply->name,
                    'reference' => $supply->reference,
                    'packaging' => $supply->packaging,
                    'suppliers' => $supply->suppliers->map(function ($supplier) {
                        return [
                            'id' => $supplier->id,
                            'name' => $supplier->name,
                            'unit_price' => $supplier->pivot->unit_price,
                        ];
                    }),
                'stock_items' => $supply->stockItems->map(function ($stockItem) {
                    return [
                        'local_alert_threshold' => $stockItem->local_alert_threshold,
                        'estimated_quantity' => $stockItem->estimated_quantity,
                    ];
                }),
                ];
            });

        return Inertia::render('Orders/Create', [
            'suppliers' => $suppliers,
            'supplies' => $supplies,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:order_date',
            'items' => 'required|array|min:1',
            'items.*.supply_id' => 'required|exists:supplies,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-' . date('YmdHis'),
            'supplier_id' => $validated['supplier_id'],
            'order_date' => $validated['order_date'],
            'expected_delivery_date' => $validated['expected_delivery_date'],
            'status' => 'pending',
            'user_id' => Auth::id(),
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'supply_id' => $item['supply_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['supplier', 'user', 'items.supply.suppliers' => function ($query) use ($order) {
            $query->where('suppliers.id', $order->supplier_id)
                ->select('suppliers.id', 'suppliers.name')
                ->withPivot('supplier_reference');
        }, 'deliveries.user', 'deliveries.items']);

        $items = $order->items->map(function ($item) {
            $supplier = $item->supply->suppliers->first();
            return [
                'id' => $item->id,
                'supply' => [
                    'id' => $item->supply->id,
                    'reference' => $item->supply->reference,
                    'name' => $item->supply->name,
                    'packaging' => $item->supply->packaging,
                    'supplier_reference' => $supplier ? $supplier->pivot->supplier_reference : null,
                ],
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ];
        });

        $total_amount = $order->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'supplier' => [
                    'id' => $order->supplier->id,
                    'name' => $order->supplier->name,
                    'catalog_url' => $order->supplier->catalog_url,
                ],
                'order_date' => $order->order_date->format('Y-m-d'),
                'expected_delivery_date' => $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : null,
                'status' => $order->status,
                'total_amount' => $total_amount,
                'user' => [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                ],
                'items' => $items,
                'deliveries' => $order->deliveries->map(function ($delivery) {
                    return [
                        'id' => $delivery->id,
                        'delivery_date' => $delivery->delivery_date->format('Y-m-d'),
                        'status' => $delivery->status,
                        'items' => $delivery->items,
                        'user' => [
                            'id' => $delivery->user->id,
                            'name' => $delivery->user->name,
                        ],
                    ];
                }),
            ],
        ]);
    }

    public function exportExcel(Order $order)
    {
        try {
            $order->load(['supplier', 'items.supply.suppliers' => function ($query) use ($order) {
                $query->where('suppliers.id', $order->supplier_id)
                    ->select('suppliers.id', 'suppliers.name')
                    ->withPivot('supplier_reference');
            }]);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'Numéro de Commande');
            $sheet->setCellValue('B1', $order->order_number);
            $sheet->setCellValue('A2', 'Fournisseur');
            $sheet->setCellValue('B2', $order->supplier->name);
            $sheet->setCellValue('A3', 'Date de Commande');
            $sheet->setCellValue('B3', $order->order_date->format('Y-m-d'));
            $sheet->setCellValue('A4', 'Date de Livraison Prévue');
            $sheet->setCellValue('B4', $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : 'Non spécifié');

            // Set items headers
            $sheet->setCellValue('A6', 'Référence');
            $sheet->setCellValue('B6', 'Référence Fournisseur');
            $sheet->setCellValue('C6', 'Nom');
            $sheet->setCellValue('D6', 'Conditionnement');
            $sheet->setCellValue('E6', 'Quantité');
            $sheet->setCellValue('F6', 'Prix Unitaire');
            $sheet->setCellValue('G6', 'Total');

            // Add items
            $row = 7;
            $total_amount = 0;
            foreach ($order->items as $item) {
                $supplier = $item->supply->suppliers->first();
                $item_total = $item->quantity * $item->unit_price;
                $total_amount += $item_total;

                $sheet->setCellValue('A' . $row, $item->supply->reference);
                $sheet->setCellValue('B' . $row, $supplier ? $supplier->pivot->supplier_reference : '-');
                $sheet->setCellValue('C' . $row, $item->supply->name);
                $sheet->setCellValue('D' . $row, $item->supply->packaging);
                $sheet->setCellValue('E' . $row, $item->quantity);
                $sheet->setCellValue('F' . $row, $item->unit_price);
                $sheet->setCellValue('G' . $row, $item_total);
                $row++;
            }

            // Add total row
            $sheet->setCellValue('F' . $row, 'Montant Total');
            $sheet->setCellValue('G' . $row, $total_amount);

            // Auto-size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create the Excel file
            $writer = new Xlsx($spreadsheet);
            $filename = 'order_' . $order->order_number . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            Log::error('Error exporting order to Excel: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->with('error', 'Une erreur est survenue lors de l\'exportation de la commande. Veuillez réessayer.');
        }
    }

    public function suggestOrders()
    {
        $suggestions = Supply::select('id', 'name', 'reference')
            ->withSum('stockItems', 'estimated_quantity')
            ->with(['stockItems' => function ($query) {
                $query->select('supply_id', 'local_alert_threshold', 'location_id')
                    ->whereRaw('estimated_quantity <= local_alert_threshold');
            }, 'suppliers' => function ($query) {
                $query->select('suppliers.id', 'suppliers.name')
                    ->withPivot('unit_price');
            }])
            ->whereHas('stockItems', function ($query) {
                $query->whereRaw('estimated_quantity <= local_alert_threshold');
            })
            ->get()
            ->map(function ($supply) {
                $stockItem = $supply->stockItems->first();
                return [
                    'id' => $supply->id,
                    'reference' => $supply->reference,
                    'name' => $supply->name,
                    'location' => $stockItem->location?->name ?? 'N/A',
                    'current_stock' => $supply->stock_items_sum_estimated_quantity ?? 0,
                    'threshold' => $stockItem->local_alert_threshold,
                    'needed_quantity' => $stockItem->local_alert_threshold - ($supply->stock_items_sum_estimated_quantity ?? 0),
                    'suppliers' => $supply->suppliers->map(function ($supplier) {
                        return [
                            'id' => $supplier->id,
                            'name' => $supplier->name,
                            'unit_price' => $supplier->pivot->unit_price,
                        ];
                    }),
                ];
            });

        return Inertia::render('Orders/Suggestions', [
            'suggestions' => $suggestions,
        ]);
    }

    public function validate(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be validated.');
        }

        $order->update(['status' => 'validated']);

        return back()->with('success', 'Order validated successfully.');
    }

    public function cancel(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function edit(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Seules les commandes en attente peuvent être modifiées.');
        }

        $suppliers = Supplier::select('id', 'name')->get();
        $supplies = Supply::select('id', 'name', 'reference', 'packaging')
            ->with(['suppliers' => function ($query) {
                $query->select('suppliers.id', 'suppliers.name')
                    ->withPivot('unit_price');
        }, 'stockItems' => function ($query) {
            $query->select('supply_id', 'local_alert_threshold', 'estimated_quantity');
            }])
            ->get()
            ->map(function ($supply) {
                return [
                    'id' => $supply->id,
                    'name' => $supply->name,
                    'reference' => $supply->reference,
                    'packaging' => $supply->packaging,
                    'suppliers' => $supply->suppliers->map(function ($supplier) {
                        return [
                            'id' => $supplier->id,
                            'name' => $supplier->name,
                            'unit_price' => $supplier->pivot->unit_price,
                        ];
                    }),
                'stock_items' => $supply->stockItems->map(function ($stockItem) {
                    return [
                        'local_alert_threshold' => $stockItem->local_alert_threshold,
                        'estimated_quantity' => $stockItem->estimated_quantity,
                    ];
                }),
                ];
            });

        return Inertia::render('Orders/Edit', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'supplier_id' => $order->supplier_id,
                'order_date' => $order->order_date->format('Y-m-d'),
                'expected_delivery_date' => $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : null,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'supply_id' => $item->supply_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                    ];
                }),
            ],
            'suppliers' => $suppliers,
            'supplies' => $supplies,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Seules les commandes en attente peuvent être modifiées.');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:order_date',
            'items' => 'required|array|min:1',
            'items.*.supply_id' => 'required|exists:supplies,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $order->update([
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
            ]);

            // Supprimer les items existants
            $order->items()->delete();

            // Créer les nouveaux items
            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'supply_id' => $item['supply_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            DB::commit();
            return redirect()->route('orders.show', $order)
                ->with('success', 'Commande modifiée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la modification de la commande : ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la modification de la commande.');
        }
    }
}