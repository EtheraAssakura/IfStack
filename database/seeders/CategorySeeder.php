<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Papeterie',
                'description' => 'Fournitures de papeterie générale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Écriture',
                'description' => 'Stylos, crayons et matériel d\'écriture',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rangement',
                'description' => 'Classeurs, dossiers et matériel de rangement',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fixation',
                'description' => 'Agrafes, trombones et matériel de fixation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Informatique',
                'description' => 'Consommables informatiques',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hygiène',
                'description' => 'Produits d\'hygiène et de nettoyage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
