<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class IsfacSeeder extends Seeder
{
  public function run()
  {
    // Établissements
    DB::table('etablissements')->insert([
      [
        'nom' => 'ISFAC Poitiers',
        'adresse' => '123 rue Example',
        'ville' => 'Poitiers',
        'code_postal' => '86000',
      ],
      [
        'nom' => 'ISFAC Niort',
        'adresse' => '456 avenue Test',
        'ville' => 'Niort',
        'code_postal' => '79000',
      ],
      [
        'nom' => 'ISFAC La Rochelle',
        'adresse' => '789 boulevard Demo',
        'ville' => 'La Rochelle',
        'code_postal' => '17000',
      ],
    ]);

    // Catégories
    $categories = [
      'Papeterie',
      'Écriture',
      'Rangement',
      'Fixation',
      'Informatique',
      'Hygiène',
    ];

    foreach ($categories as $categorie) {
      DB::table('categories')->insert([
        'nom' => $categorie,
        'description' => 'Catégorie ' . $categorie,
      ]);
    }

    // Fournisseurs
    DB::table('fournisseurs')->insert([
      [
        'nom' => 'Calipage',
        'url_catalogue' => 'https://secof.calipage.fr/',
      ],
      [
        'nom' => 'ProHD',
        'url_catalogue' => 'https://www.prohd.fr/',
      ],
    ]);

    // Fournitures exemple
    DB::table('fournitures')->insert([
      [
        'nom' => 'Ramette papier A4',
        'reference_isfac' => 'PAP001',
        'conditionnement' => 'Carton de 5 ramettes',
        'seuil_alerte_global' => 10,
        'categorie_id' => 1,
      ],
      [
        'nom' => 'Stylo bille bleu',
        'reference_isfac' => 'STY001',
        'conditionnement' => 'Boîte de 50',
        'seuil_alerte_global' => 20,
        'categorie_id' => 2,
      ],
    ]);

    // Emplacements exemple
    DB::table('emplacements')->insert([
      [
        'nom' => 'Salle de réunion',
        'description' => 'Armoire principale',
        'qr_code' => 'EMP001POI',
        'etablissement_id' => 1,
      ],
      [
        'nom' => 'Salle des formateurs',
        'description' => 'Placard du fond',
        'qr_code' => 'EMP002POI',
        'etablissement_id' => 1,
      ],
    ]);

    // Stocks initiaux
    DB::table('stocks')->insert([
      [
        'fourniture_id' => 1,
        'emplacement_id' => 1,
        'quantite_estimee' => 15,
        'seuil_alerte_local' => 5,
      ],
      [
        'fourniture_id' => 2,
        'emplacement_id' => 1,
        'quantite_estimee' => 30,
        'seuil_alerte_local' => 10,
      ],
    ]);
  }
}
