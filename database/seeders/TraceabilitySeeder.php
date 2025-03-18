<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TraceabilitySeeder extends Seeder
{
    public function run(): void
    {
        // Mouvements de stock
        DB::table('stock_movements')->insert([
            [
                'supply_id' => DB::table('supplies')->where('reference', 'PAP-A4-80')->value('id'),
                'location_id' => DB::table('locations')->first()->id,
                'user_id' => DB::table('users')->where('email', 'celine.dupuis@isfac.fr')->value('id'),
                'type' => 'in',
                'quantity' => 50,
                'previous_quantity' => 100,
                'new_quantity' => 150,
                'reason' => 'Réception livraison',
                'order_id' => DB::table('orders')->first()->id,
                'delivery_id' => DB::table('deliveries')->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => DB::table('supplies')->where('reference', 'STY-BIC-BL')->value('id'),
                'location_id' => DB::table('locations')->first()->id,
                'user_id' => DB::table('users')->where('email', 'celine.dupuis@isfac.fr')->value('id'),
                'type' => 'out',
                'quantity' => -20,
                'previous_quantity' => 150,
                'new_quantity' => 130,
                'reason' => 'Consommation',
                'order_id' => null,
                'delivery_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Journal des actions
        DB::table('activity_logs')->insert([
            [
                'user_id' => DB::table('users')->where('email', 'celine.dupuis@isfac.fr')->value('id'),
                'action' => 'create',
                'entity_type' => 'order',
                'entity_id' => DB::table('orders')->first()->id,
                'old_values' => null,
                'new_values' => json_encode([
                    'reference' => 'CMD-2024-001',
                    'status' => 'en_cours'
                ]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Journal des erreurs
        DB::table('error_logs')->insert([
            [
                'user_id' => DB::table('users')->where('email', 'celine.dupuis@isfac.fr')->value('id'),
                'error_type' => 'validation',
                'message' => 'Quantité invalide pour le mouvement de stock',
                'stack_trace' => 'ValidationException: La quantité doit être supérieure à 0',
                'context' => json_encode([
                    'supply_id' => DB::table('supplies')->where('reference', 'PAP-A4-80')->value('id'),
                    'quantity' => -5
                ]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Scans QR
        DB::table('qr_scan_logs')->insert([
            [
                'user_id' => DB::table('users')->where('email', 'celine.dupuis@isfac.fr')->value('id'),
                'location_id' => DB::table('locations')->first()->id,
                'action_type' => 'inventory',
                'scanned_data' => json_encode([
                    'location' => 'A1-01',
                    'timestamp' => now()->timestamp
                ]),
                'device_info' => 'iPhone 13',
                'ip_address' => '192.168.1.100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
