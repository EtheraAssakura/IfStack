<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Order;
use App\Models\Supply;
use App\Models\Delivery;
use App\Models\StockItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Stocks en alerte
        $alertStocks = StockItem::with(['supply', 'location.site'])
            ->whereRaw('estimated_quantity <= COALESCE(local_alert_threshold, (SELECT alert_threshold FROM supplies WHERE id = stock_items.supply_id))')
            ->get();

        // Alertes non traitées
        $unhandledAlerts = Alert::with(['stockItem.supply', 'stockItem.location.site', 'user'])
            ->where('processed', false)
            ->orderBy('created_at', 'desc')
            ->get();

        // Livraisons de la semaine
        $weekDeliveries = Delivery::with(['order', 'user', 'items.location.site'])
            ->whereBetween('expected_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->get();

        // Top 5 des fournitures les plus commandées
        $topSupplies = DB::table('order_items')
            ->join('supplies', 'supplies.id', '=', 'order_items.supply_id')
            ->select('supplies.name', 'supplies.reference', DB::raw('SUM(quantity) as total_ordered'))
            ->groupBy('supplies.id', 'supplies.name', 'supplies.reference')
            ->orderByDesc('total_ordered')
            ->limit(5)
            ->get();

        // Statistiques globales
        $stats = [
            'total_supplies' => Supply::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'planned_deliveries' => Delivery::where('status', 'planned')->count(),
            'unhandled_alerts' => $unhandledAlerts->count(),
        ];

        // Commandes par mois
        $ordersByMonth = DB::table('orders')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return Inertia::render('Dashboard', [
            'alertStocks' => $alertStocks,
            'unhandledAlerts' => $unhandledAlerts,
            'weekDeliveries' => $weekDeliveries,
            'topSupplies' => $topSupplies,
            'stats' => $stats,
            'ordersByMonth' => $ordersByMonth,
        ]);
    }

    public function generateWeeklyReport()
    {
        // Stocks en alerte
        $alertStocks = StockItem::with(['supply', 'location.site'])
            ->whereRaw('estimated_quantity <= COALESCE(local_alert_threshold, (SELECT alert_threshold FROM supplies WHERE id = stock_items.supply_id))')
            ->get();

        // Livraisons effectuées et planifiées
        $completedDeliveries = Delivery::with(['order', 'user', 'items.location.site'])
            ->where('status', 'completed')
            ->whereBetween('expected_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->get();

        $plannedDeliveries = Delivery::with(['order', 'user', 'items.location.site'])
            ->where('status', 'planned')
            ->whereBetween('expected_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->get();

        // Top produits en alerte
        $topAlertProducts = DB::table('alerts')
            ->join('stock_items', 'stock_items.id', '=', 'alerts.stock_item_id')
            ->join('supplies', 'supplies.id', '=', 'stock_items.supply_id')
            ->select('supplies.name', 'supplies.reference', DB::raw('COUNT(*) as total_alerts'))
            ->groupBy('supplies.id', 'supplies.name', 'supplies.reference')
            ->orderByDesc('total_alerts')
            ->limit(5)
            ->get();

        $data = [
            'date' => Carbon::now()->format('d/m/Y'),
            'alertStocks' => $alertStocks,
            'completedDeliveries' => $completedDeliveries,
            'plannedDeliveries' => $plannedDeliveries,
            'topAlertProducts' => $topAlertProducts,
        ];

        return response()->json($data);
    }
}
