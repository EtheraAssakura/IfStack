<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si la colonne catalog_url n'existe pas déjà dans supplier_supply
        if (!Schema::hasColumn('supplier_supply', 'catalog_url')) {
            Schema::table('supplier_supply', function (Blueprint $table) {
                $table->string('catalog_url')->nullable()->after('unit_price');
            });
        }

        // Copier les données de catalog_url de supplies vers supplier_supply
        DB::statement('
            UPDATE supplier_supply ss
            JOIN supplies s ON ss.supply_id = s.id
            SET ss.catalog_url = s.catalog_url
            WHERE s.catalog_url IS NOT NULL
        ');

        // Vérifier si la colonne catalog_url existe dans supplies avant de la supprimer
        if (Schema::hasColumn('supplies', 'catalog_url')) {
            Schema::table('supplies', function (Blueprint $table) {
                $table->dropColumn('catalog_url');
            });
        }
    }

    public function down(): void
    {
        // Vérifier si la colonne catalog_url n'existe pas déjà dans supplies
        if (!Schema::hasColumn('supplies', 'catalog_url')) {
            Schema::table('supplies', function (Blueprint $table) {
                $table->string('catalog_url')->nullable();
            });
        }

        // Copier les données de catalog_url de supplier_supply vers supplies
        DB::statement('
            UPDATE supplies s
            JOIN supplier_supply ss ON s.id = ss.supply_id
            SET s.catalog_url = ss.catalog_url
            WHERE ss.catalog_url IS NOT NULL
        ');

        // Ne pas supprimer la colonne catalog_url de supplier_supply car elle fait partie de la structure initiale
    }
};
