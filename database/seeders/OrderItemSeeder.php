<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les IDs des commandes
        $cmd001Id = DB::table('orders')
            ->where('reference', 'CMD-2024-001')
            ->value('id');

        $cmd002Id = DB::table('orders')
            ->where('reference', 'CMD-2024-002')
            ->value('id');

        // Récupérer les IDs des fournitures
        $papierA4Id = DB::table('supplies')
            ->where('reference', 'PAP-A4-80')
            ->value('id');

        $styloId = DB::table('supplies')
            ->where('reference', 'STY-BIC-BL')
            ->value('id');

        $classeurId = DB::table('supplies')
            ->where('reference', 'CLA-A4-80')
            ->value('id');

        // Créer les lignes de commande
        DB::table('order_items')->insert([
            // Lignes pour CMD-2024-001
            [
                'order_id' => $cmd001Id,
                'supply_id' => $papierA4Id,
                'quantity' => 50,
                'unit_price' => 4.50,
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'order_id' => $cmd001Id,
                'supply_id' => $styloId,
                'quantity' => 200,
                'unit_price' => 0.35,
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            // Lignes pour CMD-2024-002
            [
                'order_id' => $cmd002Id,
                'supply_id' => $classeurId,
                'quantity' => 100,
                'unit_price' => 2.75,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ]);
    }
}
