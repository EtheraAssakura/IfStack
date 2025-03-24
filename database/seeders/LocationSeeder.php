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
        $angoulemeId = DB::table('sites')->where('name', 'ISFAC Angoulême')->value('id');

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
            [
                'name' => 'Salle informatique',
                'description' => 'Armoire de stockage dans la salle informatique',
                'site_id' => $poitiersId,
                'qr_code' => 'POI-SI-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bureau direction',
                'description' => 'Placard du bureau de direction',
                'site_id' => $poitiersId,
                'qr_code' => 'POI-BD-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle de formation',
                'description' => 'Armoire de stockage dans la salle de formation',
                'site_id' => $niortId,
                'qr_code' => 'NIO-SF-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Local technique',
                'description' => 'Stockage dans le local technique',
                'site_id' => $niortId,
                'qr_code' => 'NIO-LT-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle des professeurs',
                'description' => 'Placard dans la salle des professeurs',
                'site_id' => $laRochelleId,
                'qr_code' => 'LR-SP-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Local photocopieur',
                'description' => 'Stockage près du photocopieur',
                'site_id' => $laRochelleId,
                'qr_code' => 'LR-PH-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bureau administratif',
                'description' => 'Stockage principal du site d\'Angoulême',
                'site_id' => $angoulemeId,
                'qr_code' => 'ANG-BA-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle de réunion',
                'description' => 'Armoire dans la salle de réunion',
                'site_id' => $angoulemeId,
                'qr_code' => 'ANG-SR-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Local technique',
                'description' => 'Stockage dans le local technique',
                'site_id' => $angoulemeId,
                'qr_code' => 'ANG-LT-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
