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
        $marrakechId = DB::table('sites')->where('name', 'ISFAC Marrakech')->value('id');

        DB::table('locations')->insert([
            [
                'name' => 'Salle de réunion',
                'description' => 'Armoire de stockage dans la salle de réunion',
                'site_id' => $poitiersId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle des formateurs',
                'description' => 'Placard principal de la salle des formateurs',
                'site_id' => $poitiersId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bureau administratif',
                'description' => 'Stockage principal du site de Niort',
                'site_id' => $niortId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Accueil',
                'description' => 'Armoire de stockage à l\'accueil',
                'site_id' => $laRochelleId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle informatique',
                'description' => 'Armoire de stockage dans la salle informatique',
                'site_id' => $poitiersId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bureau direction',
                'description' => 'Placard du bureau de direction',
                'site_id' => $poitiersId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle de formation',
                'description' => 'Armoire de stockage dans la salle de formation',
                'site_id' => $niortId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Local technique',
                'description' => 'Stockage dans le local technique',
                'site_id' => $niortId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle des professeurs',
                'description' => 'Placard dans la salle des professeurs',
                'site_id' => $laRochelleId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Local photocopieur',
                'description' => 'Stockage près du photocopieur',
                'site_id' => $laRochelleId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bureau administratif',
                'description' => 'Stockage principal du site de Marrakech',
                'site_id' => $marrakechId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle de réunion',
                'description' => 'Armoire dans la salle de réunion',
                'site_id' => $marrakechId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Local technique',
                'description' => 'Stockage dans le local technique',
                'site_id' => $marrakechId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
