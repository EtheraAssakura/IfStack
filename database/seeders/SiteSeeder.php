<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sites')->insert([
            [
                'name' => 'ISFAC Poitiers',
                'address' => '27 Rue Briand',
                'city' => 'Poitiers',
                'postal_code' => '86000',
                'is_headquarters' => true,
                'slug' => 'isfac-poitiers',
                'email' => 'contact@isfac.fr',
                'phone' => '05 49 37 37 37',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ISFAC Niort',
                'address' => '10 Rue Jean Jaurès',
                'city' => 'Niort',
                'postal_code' => '79000',
                'is_headquarters' => false,
                'slug' => 'isfac-niort',
                'email' => 'contact@isfac.fr',
                'phone' => '05 49 37 37 37',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ISFAC La Rochelle',
                'address' => '15 Rue des Fleurs',
                'city' => 'La Rochelle',
                'postal_code' => '17000',
                'is_headquarters' => false,
                'slug' => 'isfac-la-rochelle',
                'email' => 'contact@isfac.fr',
                'phone' => '05 49 37 37 37',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ISFAC Marrakech',
                'address' => '15 Rue des Fleurs',
                'city' => 'Marrakech',
                'postal_code' => '40000',
                'is_headquarters' => false,
                'slug' => 'isfac-marrakech',
                'email' => 'contact@isfac.fr',
                'phone' => '05 49 37 37 37',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
