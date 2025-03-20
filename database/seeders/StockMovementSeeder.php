<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockMovement;
use App\Models\Stock;
use App\Models\User;

class StockMovementSeeder extends Seeder
{
    public function run()
    {
        $stocks = Stock::all();
        $users = User::all();
        $movementTypes = ['in', 'out', 'transfer'];

        foreach ($stocks as $stock) {
            // Créer 5 mouvements aléatoires pour chaque stock
            for ($i = 0; $i < 5; $i++) {
                $quantity = rand(1, 10);
                $type = $movementTypes[array_rand($movementTypes)];

                // Ajuster la quantité en fonction du type de mouvement
                if ($type === 'out') {
                    $quantity = min($quantity, $stock->quantity);
                }

                StockMovement::create([
                    'stock_id' => $stock->id,
                    'user_id' => $users->random()->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'reference' => 'MOV-' . strtoupper(uniqid()),
                    'notes' => 'Mouvement initial de stock',
                    'created_at' => now()->subDays(rand(1, 30))
                ]);

                // Mettre à jour la quantité du stock
                if ($type === 'in') {
                    $stock->quantity += $quantity;
                } elseif ($type === 'out') {
                    $stock->quantity -= $quantity;
                }
                $stock->save();
            }
        }
    }
}
