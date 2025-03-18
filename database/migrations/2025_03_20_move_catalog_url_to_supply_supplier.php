<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter la colonne catalog_url à la table supply_supplier
        Schema::table('supply_supplier', function (Blueprint $table) {
            $table->string('catalog_url')->nullable()->after('unit_price');
        });

        // Copier les données de catalog_url de supplies vers supply_supplier
        DB::statement('
            UPDATE supply_supplier ss
            JOIN supplies s ON ss.supply_id = s.id
            SET ss.catalog_url = s.catalog_url
        ');

        // Supprimer la colonne catalog_url de la table supplies
        Schema::table('supplies', function (Blueprint $table) {
            $table->dropColumn('catalog_url');
        });
    }

    public function down(): void
    {
        // Ajouter la colonne catalog_url à la table supplies
        Schema::table('supplies', function (Blueprint $table) {
            $table->string('catalog_url')->nullable();
        });

        // Copier les données de catalog_url de supply_supplier vers supplies
        DB::statement('
            UPDATE supplies s
            JOIN supply_supplier ss ON s.id = ss.supply_id
            SET s.catalog_url = ss.catalog_url
            WHERE ss.catalog_url IS NOT NULL
        ');

        // Supprimer la colonne catalog_url de la table supply_supplier
        Schema::table('supply_supplier', function (Blueprint $table) {
            $table->dropColumn('catalog_url');
        });
    }
};
