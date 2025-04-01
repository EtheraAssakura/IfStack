<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::with(['stockItem.supply', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'title' => $alert->title,
                    'comment' => $alert->comment,
                    'type' => $alert->type,
                    'processed' => $alert->processed,
                    'created_at' => $alert->created_at->format('d/m/Y H:i'),
                    'stock_item' => [
                        'id' => $alert->stockItem->id,
                        'supply' => [
                            'id' => $alert->stockItem->supply->id,
                            'name' => $alert->stockItem->supply->name,
                            'reference' => $alert->stockItem->supply->reference
                        ],
                        'quantity' => $alert->stockItem->quantity,
                        'local_alert_threshold' => $alert->stockItem->local_alert_threshold
                    ],
                    'user' => [
                        'id' => $alert->user->id,
                        'name' => $alert->user->name
                    ]
                ];
            });

        return Inertia::render('Alerts/Index', [
            'alerts' => $alerts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'type' => 'required|in:low_stock,out_of_stock',
            'comment' => 'nullable|string'
        ]);

        $stockItem = StockItem::findOrFail($validated['stock_item_id']);

        $alert = Alert::create([
            'stock_item_id' => $validated['stock_item_id'],
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'comment' => $validated['comment'],
            'title' => $this->generateAlertTitle($validated['type'], $stockItem)
        ]);

        return back()->with('success', 'Alerte créée avec succès.');
    }

    public function process(Alert $alert)
    {
        $alert->update(['processed' => true]);
        return back()->with('success', 'Alerte traitée avec succès.');
    }

    public function destroy(Alert $alert)
    {
        $alert->delete();
        return back()->with('success', 'Alerte supprimée avec succès.');
    }

    private function generateAlertTitle($type, $stockItem)
    {
        switch ($type) {
            case 'low_stock':
                return "Stock faible pour {$stockItem->supply->name}";
            case 'out_of_stock':
                return "Rupture de stock pour {$stockItem->supply->name}";
            default:
                return "Alerte de stock pour {$stockItem->supply->name}";
        }
    }
}
