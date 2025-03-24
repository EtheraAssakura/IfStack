<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'name' => 'Calipage',
                'catalog_url' => 'https://secof.calipage.fr/',
                'contact_name' => 'Calipage',
                'email' => 'contact@calipage.fr',
                'phone' => '+33 1 40 46 46 46',
                'address' => '123 Rue de la Paix',
                'city' => 'Paris',
                'postal_code' => '75000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ProHD',
                'catalog_url' => 'https://www.prohd.fr/',
                'contact_name' => 'ProHD',
                'email' => 'contact@prohd.fr',
                'phone' => '+33 1 40 46 46 46',
                'address' => '123 Rue de la Paix',
                'city' => 'Paris',
                'postal_code' => '75000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Made in Thé',
                'catalog_url' => 'https://www.grossiste-de-the.fr/',
                'contact_name' => 'Made in Thé',
                'email' => 'contact@madeinthe.fr',
                'phone' => '+33 1 40 46 46 46',
                'address' => '302 Chemin du Grand Babol',
                'city' => 'Simiane-Collongue',
                'postal_code' => '13109',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
