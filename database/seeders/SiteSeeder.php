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
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
