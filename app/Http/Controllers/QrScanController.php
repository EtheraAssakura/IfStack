<?php

namespace App\Http\Controllers;

use App\Models\QrScan;
use App\Models\StockItem;
use App\Traits\GeneratesQrCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class QrScanController extends Controller
{
    use GeneratesQrCodes;

    public function index()
    {
        $scans = QrScan::with(['stockItem.supply', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($scan) {
                return [
                    'id' => $scan->id,
                    'stock_item' => [
                        'id' => $scan->stockItem->id,
                        'supply' => [
                            'id' => $scan->stockItem->supply->id,
                            'name' => $scan->stockItem->supply->name,
                            'reference' => $scan->stockItem->supply->reference
                        ],
                        'quantity' => $scan->stockItem->quantity,
                        'local_alert_threshold' => $scan->stockItem->local_alert_threshold
                    ],
                    'user' => [
                        'id' => $scan->user->id,
                        'name' => $scan->user->name
                    ],
                    'created_at' => $scan->created_at->format('d/m/Y H:i')
                ];
            });

        return Inertia::render('QrScans/Index', [
            'scans' => $scans
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $stockItem = StockItem::findOrFail($validated['stock_item_id']);

        QrScan::create([
            'stock_item_id' => $validated['stock_item_id'],
            'user_id' => Auth::id(),
            'quantity' => $validated['quantity']
        ]);

        return back()->with('success', 'Scan enregistré avec succès.');
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string'
        ]);

        $stockItem = StockItem::where('qr_code', $validated['qr_code'])
            ->with('supply')
            ->firstOrFail();

        return Inertia::render('QrScans/Show', [
            'stock_item' => [
                'id' => $stockItem->id,
                'supply' => [
                    'id' => $stockItem->supply->id,
                    'name' => $stockItem->supply->name,
                    'reference' => $stockItem->supply->reference
                ],
                'quantity' => $stockItem->quantity,
                'local_alert_threshold' => $stockItem->local_alert_threshold
            ]
        ]);
    }

    public function generateQrCode(StockItem $stockItem)
    {
        // Générer un code QR unique
        $qrCode = uniqid('QR_', true);

        $stockItem->update(['qr_code' => $qrCode]);

        return back()->with('success', 'Code QR généré avec succès.');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'path' => 'required|string'
        ]);

        $url = $this->generateQrCode($validated['url'], $validated['path']);

        return response()->json([
            'url' => $url
        ]);
    }
}
