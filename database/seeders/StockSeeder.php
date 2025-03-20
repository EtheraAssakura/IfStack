<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Supply;
use App\Models\Location;

class StockSeeder extends Seeder
{
    public function run()
    {
        $supplies = Supply::all();
        $locations = Location::all();

        foreach ($supplies as $supply) {
            // Créer un stock pour chaque fourniture dans chaque emplacement
            foreach ($locations as $location) {
                // Générer une quantité aléatoire entre le minimum et le maximum
                $quantity = rand($supply->minimum_stock, $supply->maximum_stock);

                Stock::create([
                    'supply_id' => $supply->id,
                    'location_id' => $location->id,
                    'quantity' => $quantity,
                    'batch_number' => 'BATCH-' . strtoupper(uniqid()),
                    'expiry_date' => now()->addMonths(rand(1, 12))
                ]);
            }
        }
    }
}
