<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportingSeeder extends Seeder
{
    public function run(): void
    {
        // Statistiques d'utilisation
        DB::table('usage_statistics')->insert([
            [
                'supply_id' => DB::table('supplies')->where('reference', 'PAP-A4-80')->value('id'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC Poitiers')->value('id'),
                'quantity_used' => 150,
                'total_cost' => 375.50,
                'period_start' => '2024-01-01',
                'period_end' => '2024-03-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'STY-BIC-BL')->value('id'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC Niort')->value('id'),
                'quantity_used' => 75,
                'total_cost' => 187.25,
                'period_start' => '2024-01-01',
                'period_end' => '2024-03-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Prévisions
        DB::table('forecasts')->insert([
            [
                'supply_id' => DB::table('supplies')->where('reference', 'PAP-A4-80')->value('id'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC Poitiers')->value('id'),
                'predicted_quantity' => 200,
                'predicted_cost' => 500.00,
                'prediction_date' => '2024-03-15',
                'target_date' => '2024-06-30',
                'calculation_parameters' => json_encode([
                    'historical_period' => 90,
                    'confidence_level' => 0.95,
                    'seasonal_factor' => 1.2
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // KPIs
        DB::table('kpi_metrics')->insert([
            [
                'name' => 'Taux de rotation des stocks',
                'category' => 'stock',
                'value' => 4.5,
                'unit' => 'ratio',
                'site_id' => DB::table('sites')->where('name', 'ISFAC Poitiers')->value('id'),
                'measurement_date' => '2024-03-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Délai moyen de livraison',
                'category' => 'deliveries',
                'value' => 3.2,
                'unit' => 'jours',
                'site_id' => DB::table('sites')->where('name', 'ISFAC Poitiers')->value('id'),
                'measurement_date' => '2024-03-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Rapports
        DB::table('reports')->insert([
            [
                'name' => 'Rapport trimestriel des stocks Q1 2024',
                'type' => 'stock',
                'parameters' => json_encode([
                    'period' => 'Q1',
                    'year' => 2024,
                    'sites' => ['ISFAC Poitiers', 'ISFAC Niort']
                ]),
                'data' => json_encode([
                    'total_value' => 15000.00,
                    'low_stock_items' => 5,
                    'overstock_items' => 3
                ]),
                'user_id' => DB::table('users')->where('email', 'celine.dupuis@isfac.fr')->value('id'),
                'site_id' => null,
                'generated_at' => '2024-03-31 23:59:59',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
