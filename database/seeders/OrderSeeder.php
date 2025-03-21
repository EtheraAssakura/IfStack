<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les IDs nécessaires
        $calipageId = DB::table('suppliers')->where('name', 'Calipage')->value('id');
        $celineId = DB::table('users')->where('email', 'celine.dupuis@isfac.fr')->value('id');

        // Créer les commandes
        DB::table('orders')->insert([
            [
                'order_number' => 'ORD-20250321105632',
                'supplier_id' => $calipageId,
                'user_id' => $celineId,
                'status' => 'pending',
                'order_date' => now()->subDays(15),
                'expected_delivery_date' => now()->subDays(10),
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'order_number' => 'ORD-20250322105633',
                'supplier_id' => $calipageId,
                'user_id' => $celineId,
                'status' => 'validated',
                'order_date' => now()->subDays(10),
                'expected_delivery_date' => now()->subDays(5),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(5),
            ],
            [
                'order_number' => 'ORD-20250323105634',
                'supplier_id' => $calipageId,
                'user_id' => $celineId,
                'status' => 'cancelled',
                'order_date' => now()->subDays(5),
                'expected_delivery_date' => now()->subDays(2),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(3),
            ],
        ]);
    }
}
