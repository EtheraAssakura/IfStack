<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les IDs des sites
        $poitiersId = DB::table('sites')->where('name', 'ISFAC Poitiers')->value('id');
        $niortId = DB::table('sites')->where('name', 'ISFAC Niort')->value('id');
        $laRochelleId = DB::table('sites')->where('name', 'ISFAC La Rochelle')->value('id');

        DB::table('locations')->insert([
            [
                'name' => 'Salle de réunion',
                'description' => 'Armoire de stockage dans la salle de réunion',
                'site_id' => $poitiersId,
                'qr_code' => 'POI-SR-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle des formateurs',
                'description' => 'Placard principal de la salle des formateurs',
                'site_id' => $poitiersId,
                'qr_code' => 'POI-SF-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bureau administratif',
                'description' => 'Stockage principal du site de Niort',
                'site_id' => $niortId,
                'qr_code' => 'NIO-BA-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Accueil',
                'description' => 'Armoire de stockage à l\'accueil',
                'site_id' => $laRochelleId,
                'qr_code' => 'LR-ACC-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
