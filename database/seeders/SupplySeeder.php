<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplySeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les IDs des catégories
        $papeterieId = DB::table('categories')->where('name', 'Papeterie')->value('id');
        $ecritureId = DB::table('categories')->where('name', 'Écriture')->value('id');
        $rangementId = DB::table('categories')->where('name', 'Rangement')->value('id');

        // Récupérer les IDs des fournisseurs
        $calipageId = DB::table('suppliers')->where('name', 'Calipage')->value('id');
        $prohdId = DB::table('suppliers')->where('name', 'ProHD')->value('id');

        // Créer les fournitures
        DB::table('supplies')->insert([
            [
                'name' => 'Ramette papier A4',
                'reference' => 'PAP-A4-80',
                'packaging' => 'Carton de 5 ramettes',
                'category_id' => $papeterieId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stylo BIC bleu',
                'reference' => 'STY-BIC-BL',
                'packaging' => 'Boîte de 50',
                'category_id' => $ecritureId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Classeur A4',
                'reference' => 'CLA-A4-80',
                'packaging' => 'Lot de 10',
                'category_id' => $rangementId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Récupérer les IDs des fournitures créées
        $papierA4Id = DB::table('supplies')->where('reference', 'PAP-A4-80')->value('id');
        $styloId = DB::table('supplies')->where('reference', 'STY-BIC-BL')->value('id');
        $classeurId = DB::table('supplies')->where('reference', 'CLA-A4-80')->value('id');

        // Créer les liens avec les fournisseurs
        DB::table('supply_supplier')->insert([
            // Liens Calipage
            [
                'supply_id' => $papierA4Id,
                'supplier_id' => $calipageId,
                'supplier_reference' => 'CAL-PAP-001',
                'unit_price' => 4.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => $styloId,
                'supplier_id' => $calipageId,
                'supplier_reference' => 'CAL-STY-001',
                'unit_price' => 0.35,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => $classeurId,
                'supplier_id' => $calipageId,
                'supplier_reference' => 'CAL-CLA-001',
                'unit_price' => 2.75,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Liens ProHD
            [
                'supply_id' => $papierA4Id,
                'supplier_id' => $prohdId,
                'supplier_reference' => 'PRO-PAP-001',
                'unit_price' => 4.25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => $styloId,
                'supplier_id' => $prohdId,
                'supplier_reference' => 'PRO-STY-001',
                'unit_price' => 0.32,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supply_id' => $classeurId,
                'supplier_id' => $prohdId,
                'supplier_reference' => 'PRO-CLA-001',
                'unit_price' => 2.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
