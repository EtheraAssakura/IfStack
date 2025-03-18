<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryItemSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer l'ID de la livraison
        $deliveryId = DB::table('deliveries')
            ->where('order_id', function ($query) {
                $query->select('id')
                    ->from('orders')
                    ->where('reference', 'CMD-2024-001')
                    ->first();
            })
            ->value('id');

        // Récupérer les IDs des emplacements
        $salleReunionId = DB::table('locations')
            ->where('qr_code', 'POI-SR-001')
            ->value('id');

        $salleFormateursId = DB::table('locations')
            ->where('qr_code', 'POI-SF-001')
            ->value('id');

        // Récupérer les IDs des fournitures
        $papierA4Id = DB::table('supplies')
            ->where('reference', 'PAP-A4-80')
            ->value('id');

        $styloId = DB::table('supplies')
            ->where('reference', 'STY-BIC-BL')
            ->value('id');

        // Créer les détails de livraison
        DB::table('delivery_items')->insert([
            [
                'delivery_id' => $deliveryId,
                'location_id' => $salleReunionId,
                'supply_id' => $papierA4Id,
                'quantity' => 30,
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'delivery_id' => $deliveryId,
                'location_id' => $salleFormateursId,
                'supply_id' => $papierA4Id,
                'quantity' => 20,
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'delivery_id' => $deliveryId,
                'location_id' => $salleReunionId,
                'supply_id' => $styloId,
                'quantity' => 120,
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'delivery_id' => $deliveryId,
                'location_id' => $salleFormateursId,
                'supply_id' => $styloId,
                'quantity' => 80,
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
        ]);
    }
}
