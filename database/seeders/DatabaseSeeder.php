<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SiteSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            SupplySeeder::class,
            LocationSeeder::class,
            StockItemSeeder::class,
            OrderSeeder::class,
            DeliverySeeder::class,
            AlertSeeder::class,
            ReportingSeeder::class,
            TraceabilitySeeder::class,
        ]);
    }
}
