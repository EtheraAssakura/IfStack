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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ProHD',
                'catalog_url' => 'https://www.prohd.fr/',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
