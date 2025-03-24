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
            // Nouvelles relations pour les fournitures ajoutées
            [
                'supply_id' => DB::table('supplies')->where('reference', 'CRA-HB-001')->value('id'),
                'location_id' => $salleReunionId,
                'estimated_quantity' => 50,
                'local_alert_threshold' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'GOM-WH-001')->value('id'),
                'location_id' => $salleReunionId,
                'estimated_quantity' => 30,
                'local_alert_threshold' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'SUR-YE-001')->value('id'),
                'location_id' => $salleFormateursId,
                'estimated_quantity' => 25,
                'local_alert_threshold' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'PAP-A3-80')->value('id'),
                'location_id' => $bureauNiortId,
                'estimated_quantity' => 15,
                'local_alert_threshold' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'POS-76-001')->value('id'),
                'location_id' => $accueilLRId,
                'estimated_quantity' => 20,
                'local_alert_threshold' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'CHE-CAR-001')->value('id'),
                'location_id' => $salleReunionId,
                'estimated_quantity' => 40,
                'local_alert_threshold' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'TRO-MET-001')->value('id'),
                'location_id' => $salleFormateursId,
                'estimated_quantity' => 100,
                'local_alert_threshold' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'AGD-A4-001')->value('id'),
                'location_id' => $bureauNiortId,
                'estimated_quantity' => 10,
                'local_alert_threshold' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'MAR-WH-001')->value('id'),
                'location_id' => $accueilLRId,
                'estimated_quantity' => 15,
                'local_alert_threshold' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'SCO-TRA-001')->value('id'),
                'location_id' => $salleReunionId,
                'estimated_quantity' => 12,
                'local_alert_threshold' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'CAH-A4-96')->value('id'),
                'location_id' => $salleFormateursId,
                'estimated_quantity' => 30,
                'local_alert_threshold' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'PIN-TRO-001')->value('id'),
                'location_id' => $bureauNiortId,
                'estimated_quantity' => 50,
                'local_alert_threshold' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'REG-30-001')->value('id'),
                'location_id' => $accueilLRId,
                'estimated_quantity' => 20,
                'local_alert_threshold' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'CAL-BAS-001')->value('id'),
                'location_id' => $salleReunionId,
                'estimated_quantity' => 5,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'THE-003')->value('id'),
                'location_id' => $salleFormateursId,
                'estimated_quantity' => 50,
                'local_alert_threshold' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'CIS-STA-001')->value('id'),
                'location_id' => $bureauNiortId,
                'estimated_quantity' => 8,
                'local_alert_threshold' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'PER-2T-001')->value('id'),
                'location_id' => $accueilLRId,
                'estimated_quantity' => 3,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'BLN-A5-001')->value('id'),
                'location_id' => $salleReunionId,
                'estimated_quantity' => 25,
                'local_alert_threshold' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'STB-GR-001')->value('id'),
                'location_id' => $salleFormateursId,
                'estimated_quantity' => 20,
                'local_alert_threshold' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'CLA-LEV-001')->value('id'),
                'location_id' => $bureauNiortId,
                'estimated_quantity' => 15,
                'local_alert_threshold' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'PIL-AA-001')->value('id'),
                'location_id' => $accueilLRId,
                'estimated_quantity' => 40,
                'local_alert_threshold' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'LAM-BUR-001')->value('id'),
                'location_id' => $salleReunionId,
                'estimated_quantity' => 3,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'TAP-SOU-001')->value('id'),
                'location_id' => $salleFormateursId,
                'estimated_quantity' => 8,
                'local_alert_threshold' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'CLA-USB-001')->value('id'),
                'location_id' => $bureauNiortId,
                'estimated_quantity' => 5,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'SOU-USB-001')->value('id'),
                'location_id' => $accueilLRId,
                'estimated_quantity' => 5,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'WEB-HD-001')->value('id'),
                'location_id' => $salleReunionId,
                'estimated_quantity' => 2,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'MIC-USB-001')->value('id'),
                'location_id' => $salleFormateursId,
                'estimated_quantity' => 3,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'CAS-AUD-001')->value('id'),
                'location_id' => $bureauNiortId,
                'estimated_quantity' => 4,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'HUB-USB-001')->value('id'),
                'location_id' => $accueilLRId,
                'estimated_quantity' => 3,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'CAB-HDM-001')->value('id'),
                'location_id' => $salleReunionId,
                'estimated_quantity' => 4,
                'local_alert_threshold' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
