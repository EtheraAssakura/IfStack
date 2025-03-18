<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockItemSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les IDs des fournitures
        $papierA4Id = DB::table('supplies')->where('reference', 'PAP-A4-80')->value('id');
        $styloId = DB::table('supplies')->where('reference', 'STY-BIC-BL')->value('id');
        $classeurId = DB::table('supplies')->where('reference', 'CLA-A4-80')->value('id');

        // Récupérer les IDs des emplacements
        $salleReunionId = DB::table('locations')->where('qr_code', 'POI-SR-001')->value('id');
        $salleFormateursId = DB::table('locations')->where('qr_code', 'POI-SF-001')->value('id');
        $bureauNiortId = DB::table('locations')->where('qr_code', 'NIO-BA-001')->value('id');
        $accueilLRId = DB::table('locations')->where('qr_code', 'LR-ACC-001')->value('id');

        // Insérer les stocks initiaux
        DB::table('stock_items')->insert([
            // Stocks à Poitiers - Salle de réunion
            [
                'supply_id' => $papierA4Id,
                'location_id' => $salleReunionId,
                'estimated_quantity' => 25,
                'local_alert_threshold' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => $styloId,
                'location_id' => $salleReunionId,
                'estimated_quantity' => 100,
                'local_alert_threshold' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Stocks à Poitiers - Salle des formateurs
            [
                'supply_id' => $papierA4Id,
                'location_id' => $salleFormateursId,
                'estimated_quantity' => 15,
                'local_alert_threshold' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => $classeurId,
                'location_id' => $salleFormateursId,
                'estimated_quantity' => 30,
                'local_alert_threshold' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Stocks à Niort
            [
                'supply_id' => $papierA4Id,
                'location_id' => $bureauNiortId,
                'estimated_quantity' => 10,
                'local_alert_threshold' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => $styloId,
                'location_id' => $bureauNiortId,
                'estimated_quantity' => 45,
                'local_alert_threshold' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Stocks à La Rochelle
            [
                'supply_id' => $papierA4Id,
                'location_id' => $accueilLRId,
                'estimated_quantity' => 8,
                'local_alert_threshold' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => $classeurId,
                'location_id' => $accueilLRId,
                'estimated_quantity' => 12,
                'local_alert_threshold' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
