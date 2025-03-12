<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer l'ID de l'utilisateur de La Rochelle
        $userLRId = DB::table('users')
            ->where('email', 'user.larochelle@isfac.fr')
            ->value('id');

        // Récupérer l'ID du stock de papier A4 à La Rochelle
        $papierA4Id = DB::table('supplies')
            ->where('reference', 'PAP-A4-80')
            ->value('id');

        $locationLRId = DB::table('locations')
            ->where('qr_code', 'LR-ACC-001')
            ->value('id');

        $stockLRId = DB::table('stock_items')
            ->where('supply_id', $papierA4Id)
            ->where('location_id', $locationLRId)
            ->value('id');

        // Créer une alerte pour le stock bas de papier à La Rochelle
        DB::table('alerts')->insert([
            [
                'stock_id' => $stockLRId,
                'user_id' => $userLRId,
                'type' => 'seuil_atteint',
                'comment' => 'Stock de papier très bas, besoin de réapprovisionnement urgent',
                'processed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
