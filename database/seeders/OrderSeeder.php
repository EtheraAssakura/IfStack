<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les IDs nécessaires
        $adminId = DB::table('users')
            ->where('email', 'celine.dupuis@isfac.fr')
            ->value('id');

        $calipageId = DB::table('suppliers')
            ->where('name', 'Calipage')
            ->value('id');

        $prohdId = DB::table('suppliers')
            ->where('name', 'ProHD')
            ->value('id');

        // Créer quelques commandes
        DB::table('orders')->insert([
            [
                'reference' => 'CMD-2024-001',
                'supplier_id' => $calipageId,
                'user_id' => $adminId,
                'status' => 'validee',
                'order_date' => now()->subDays(15),
                'expected_delivery_date' => now()->subDays(10),
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'reference' => 'CMD-2024-002',
                'supplier_id' => $prohdId,
                'user_id' => $adminId,
                'status' => 'en_cours',
                'order_date' => now()->subDays(5),
                'expected_delivery_date' => now()->addDays(2),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ]);
    }
}
