<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(): JsonResponse
    {
        $stocks = StockItem::with(['supply', 'location.site'])->get();
        return response()->json($stocks);
    }

    public function show(StockItem $stockItem): JsonResponse
    {
        $stockItem->load(['supply', 'location.site', 'stockMovements' => function ($query) {
            $query->latest()->limit(10);
        }]);
        return response()->json($stockItem);
    }

    public function update(Request $request, StockItem $stockItem): JsonResponse
    {
        $validated = $request->validate([
            'estimated_quantity' => 'required|integer|min:0',
            'local_alert_threshold' => 'nullable|integer|min:0'
        ]);

        DB::transaction(function () use ($stockItem, $validated, $request) {
            // Créer un mouvement de stock
            StockMovement::create([
                'supply_id' => $stockItem->supply_id,
                'location_id' => $stockItem->location_id,
                'user_id' => $request->user()->id,
                'type' => 'adjustment',
                'quantity' => $validated['estimated_quantity'] - $stockItem->estimated_quantity,
                'previous_quantity' => $stockItem->estimated_quantity,
                'new_quantity' => $validated['estimated_quantity'],
                'reason' => $request->input('reason', 'Ajustement manuel')
            ]);

            // Mettre à jour le stock
            $stockItem->update($validated);
        });

        return response()->json($stockItem);
    }

    public function transfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'supply_id' => 'required|exists:supplies,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $request) {
            $fromStock = StockItem::where([
                'location_id' => $validated['from_location_id'],
                'supply_id' => $validated['supply_id']
            ])->firstOrFail();

            $toStock = StockItem::firstOrCreate(
                [
                    'location_id' => $validated['to_location_id'],
                    'supply_id' => $validated['supply_id']
                ],
                ['estimated_quantity' => 0]
            );

            // Créer les mouvements de stock
            StockMovement::create([
                'supply_id' => $validated['supply_id'],
                'location_id' => $validated['from_location_id'],
                'user_id' => $request->user()->id,
                'type' => 'transfer_out',
                'quantity' => -$validated['quantity'],
                'previous_quantity' => $fromStock->estimated_quantity,
                'new_quantity' => $fromStock->estimated_quantity - $validated['quantity'],
                'reason' => $validated['reason'] ?? 'Transfert sortant'
            ]);

            StockMovement::create([
                'supply_id' => $validated['supply_id'],
                'location_id' => $validated['to_location_id'],
                'user_id' => $request->user()->id,
                'type' => 'transfer_in',
                'quantity' => $validated['quantity'],
                'previous_quantity' => $toStock->estimated_quantity,
                'new_quantity' => $toStock->estimated_quantity + $validated['quantity'],
                'reason' => $validated['reason'] ?? 'Transfert entrant'
            ]);

            // Mettre à jour les stocks
            $fromStock->decrement('estimated_quantity', $validated['quantity']);
            $toStock->increment('estimated_quantity', $validated['quantity']);
        });

        return response()->json(['message' => 'Transfert effectué avec succès']);
    }

    public function getMovements(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location_id' => 'nullable|exists:locations,id',
            'supply_id' => 'nullable|exists:supplies,id',
            'type' => 'nullable|string'
        ]);

        $query = StockMovement::with(['supply', 'location', 'user'])
            ->when($validated['start_date'] ?? null, function ($q, $date) {
                return $q->where('created_at', '>=', $date);
            })
            ->when($validated['end_date'] ?? null, function ($q, $date) {
                return $q->where('created_at', '<=', $date);
            })
            ->when($validated['location_id'] ?? null, function ($q, $id) {
                return $q->where('location_id', $id);
            })
            ->when($validated['supply_id'] ?? null, function ($q, $id) {
                return $q->where('supply_id', $id);
            })
            ->when($validated['type'] ?? null, function ($q, $type) {
                return $q->where('type', $type);
            })
            ->orderBy('created_at', 'desc');

        return response()->json($query->paginate(20));
    }
}
