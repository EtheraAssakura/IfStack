<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les IDs nécessaires
        $cmd001Id = DB::table('orders')
            ->where('reference', 'CMD-2024-001')
            ->value('id');

        $adminId = DB::table('users')
            ->where('email', 'celine.dupuis@isfac.fr')
            ->value('id');

        // Créer une livraison pour la commande CMD-2024-001
        DB::table('deliveries')->insert([
            [
                'order_id' => $cmd001Id,
                'user_id' => $adminId,
                'expected_date' => now()->subDays(10),
                'actual_date' => now()->subDays(9),
                'status' => 'effectuee',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(9),
            ],
        ]);
    }
}
