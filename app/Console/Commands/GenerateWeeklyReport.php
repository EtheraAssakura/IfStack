<?php

namespace App\Console\Commands;

use App\Models\Rapport;
use App\Models\Stock;
use App\Models\Commande;
use App\Models\Fourniture;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PDF;

class GenerateWeeklyReport extends Command
{
    protected $signature = 'rapports:generate-weekly';
    protected $description = 'Génère un rapport hebdomadaire du tableau de bord';

    public function handle()
    {
        // Récupérer les données du tableau de bord
        $alertStocks = Stock::with(['supply', 'location.site'])
            ->whereRaw('estimated_quantity <= local_alert_threshold')
            ->get();

        $unhandledAlerts = \App\Models\Alert::with(['stockItem.supply', 'stockItem.location.site', 'user'])
            ->where('handled', false)
            ->get();

        $weekDeliveries = \App\Models\Delivery::with(['order', 'items.location.site'])
            ->whereBetween('expected_date', [Carbon::now(), Carbon::now()->addDays(7)])
            ->get();

        $topSupplies = Fourniture::withCount('orders')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get();

        $stats = [
            'total_supplies' => Fourniture::count(),
            'pending_orders' => Commande::where('status', 'pending')->count(),
            'planned_deliveries' => \App\Models\Delivery::where('status', 'planned')->count(),
            'unhandled_alerts' => \App\Models\Alert::where('handled', false)->count(),
            'pending_requests' => \App\Models\Request::where('status', 'pending')->count(),
        ];

        $ordersByMonth = Commande::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        // Générer le PDF
        $pdf = PDF::loadView('pdf.weekly-report', compact(
            'alertStocks',
            'unhandledAlerts',
            'weekDeliveries',
            'topSupplies',
            'stats',
            'ordersByMonth'
        ));

        // Créer le nom du fichier
        $date = Carbon::now()->format('Y-m-d');
        $filename = "rapports/weekly-report-{$date}.pdf";

        // Sauvegarder le PDF
        Storage::put('public/' . $filename, $pdf->output());

        // Créer l'entrée dans la base de données
        Rapport::create([
            'name' => "Rapport général hebdomadaire du {$date}",
            'path' => $filename,
            'created_at' => Carbon::now()
        ]);

        $this->info('Rapport hebdomadaire généré avec succès !');
    }
}
