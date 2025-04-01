<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['order.supplier', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        return Inertia::render('Deliveries/Index', [
            'deliveries' => $deliveries
        ]);
    }

    public function create()
    {
        $orders = Order::where('status', 'validated')
            ->with(['supplier', 'items.supply'])
            ->get();
        return Inertia::render('Deliveries/Create', [
            'orders' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated) {
            $order = Order::findOrFail($validated['order_id']);

            $delivery = Delivery::create([
                'order_id' => $validated['order_id'],
                'delivery_date' => $validated['delivery_date'],
                'notes' => $validated['notes'],
                'user_id' => Auth::id(),
                'status' => 'pending'
            ]);

            foreach ($validated['items'] as $item) {
                $orderItem = $order->items()->findOrFail($item['order_item_id']);

                $delivery->items()->create([
                    'order_item_id' => $item['order_item_id'],
                    'quantity' => $item['quantity']
                ]);

                // Mise à jour du stock
                $stockItem = StockItem::where('supply_id', $orderItem->supply_id)
                    ->where('site_id', Auth::user()->site_id)
                    ->first();

                if ($stockItem) {
                    $stockItem->increment('quantity', $item['quantity']);
                } else {
                    StockItem::create([
                        'supply_id' => $orderItem->supply_id,
                        'site_id' => Auth::user()->site_id,
                        'quantity' => $item['quantity'],
                        'local_alert_threshold' => 0,
                        'estimated_quantity' => $item['quantity']
                    ]);
                }
            }
        });

        return redirect()->route('deliveries.index')
            ->with('success', 'Livraison créée avec succès.');
    }

    public function show(Delivery $delivery)
    {
        return Inertia::render('Deliveries/Show', [
            'delivery' => $delivery->load(['order.supplier', 'items.orderItem.supply', 'user'])
        ]);
    }

    public function edit(Delivery $delivery)
    {
        return Inertia::render('Deliveries/Edit', [
            'delivery' => $delivery->load(['order.supplier', 'items.orderItem.supply'])
        ]);
    }

    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:delivery_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $delivery) {
            $delivery->update([
                'delivery_date' => $validated['delivery_date'],
                'notes' => $validated['notes']
            ]);

            foreach ($validated['items'] as $item) {
                $deliveryItem = $delivery->items()->findOrFail($item['id']);
                $oldQuantity = $deliveryItem->quantity;

                $deliveryItem->update([
                    'quantity' => $item['quantity']
                ]);

                // Mise à jour du stock
                $stockItem = StockItem::where('supply_id', $deliveryItem->orderItem->supply_id)
                    ->where('site_id', Auth::user()->site_id)
                    ->first();

                if ($stockItem) {
                    $stockItem->decrement('quantity', $oldQuantity);
                    $stockItem->increment('quantity', $item['quantity']);
                }
            }
        });

        return redirect()->route('deliveries.index')
            ->with('success', 'Livraison mise à jour avec succès.');
    }

    public function destroy(Delivery $delivery)
    {
        DB::transaction(function () use ($delivery) {
            foreach ($delivery->items as $item) {
                $stockItem = StockItem::where('supply_id', $item->orderItem->supply_id)
                    ->where('site_id', Auth::user()->site_id)
                    ->first();

                if ($stockItem) {
                    $stockItem->decrement('quantity', $item->quantity);
                }
            }

            $delivery->delete();
        });

        return redirect()->route('deliveries.index')
            ->with('success', 'Livraison supprimée avec succès.');
    }

    public function validate(Delivery $delivery)
    {
        $delivery->update(['status' => 'validated']);
        return redirect()->route('deliveries.index')
            ->with('success', 'Livraison validée avec succès.');
    }

    public function cancel(Delivery $delivery)
    {
        DB::transaction(function () use ($delivery) {
            foreach ($delivery->items as $item) {
                $stockItem = StockItem::where('supply_id', $item->orderItem->supply_id)
                    ->where('site_id', Auth::user()->site_id)
                    ->first();

                if ($stockItem) {
                    $stockItem->decrement('quantity', $item->quantity);
                }
            }

            $delivery->update(['status' => 'cancelled']);
        });

        return redirect()->route('deliveries.index')
            ->with('success', 'Livraison annulée avec succès.');
    }
}
