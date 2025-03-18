<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('etablissements', 'sites');
        Schema::rename('fournitures', 'supplies');
        Schema::rename('fournisseurs', 'suppliers');
        Schema::rename('emplacements', 'locations');
        Schema::rename('stocks', 'stock_items');
        Schema::rename('alertes', 'alerts');
        Schema::rename('commandes', 'orders');
        Schema::rename('lignes_commande', 'order_items');
        Schema::rename('livraisons', 'deliveries');
        Schema::rename('details_livraison', 'delivery_items');
        Schema::rename('fourniture_fournisseur', 'supply_supplier');

        // Renommer les colonnes en anglais
        Schema::table('sites', function (Blueprint $table) {
            $table->renameColumn('nom', 'name');
            $table->renameColumn('adresse', 'address');
            $table->renameColumn('ville', 'city');
            $table->renameColumn('code_postal', 'postal_code');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('nom', 'name');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->renameColumn('nom', 'name');
            $table->renameColumn('url_catalogue', 'catalog_url');
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->renameColumn('nom', 'name');
            $table->renameColumn('reference_isfac', 'reference');
            $table->renameColumn('conditionnement', 'packaging');
            $table->renameColumn('url_catalogue', 'catalog_url');
            $table->renameColumn('seuil_alerte_global', 'alert_threshold');
            $table->renameColumn('categorie_id', 'category_id');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->renameColumn('nom', 'name');
            $table->renameColumn('position_plan', 'plan_position');
            $table->renameColumn('etablissement_id', 'site_id');
        });

        Schema::table('stock_items', function (Blueprint $table) {
            $table->renameColumn('fourniture_id', 'supply_id');
            $table->renameColumn('emplacement_id', 'location_id');
            $table->renameColumn('quantite_estimee', 'estimated_quantity');
            $table->renameColumn('seuil_alerte_local', 'local_alert_threshold');
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->renameColumn('commentaire', 'comment');
            $table->renameColumn('traitee', 'processed');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('fournisseur_id', 'supplier_id');
            $table->renameColumn('statut', 'status');
            $table->renameColumn('date_commande', 'order_date');
            $table->renameColumn('date_livraison_prevue', 'expected_delivery_date');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('commande_id', 'order_id');
            $table->renameColumn('fourniture_id', 'supply_id');
            $table->renameColumn('quantite', 'quantity');
            $table->renameColumn('prix_unitaire', 'unit_price');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->renameColumn('commande_id', 'order_id');
            $table->renameColumn('date_prevue', 'expected_date');
            $table->renameColumn('date_effective', 'actual_date');
            $table->renameColumn('statut', 'status');
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->renameColumn('livraison_id', 'delivery_id');
            $table->renameColumn('emplacement_id', 'location_id');
            $table->renameColumn('fourniture_id', 'supply_id');
            $table->renameColumn('quantite', 'quantity');
        });

        Schema::table('supply_supplier', function (Blueprint $table) {
            $table->renameColumn('fourniture_id', 'supply_id');
            $table->renameColumn('fournisseur_id', 'supplier_id');
            $table->renameColumn('reference_fournisseur', 'supplier_reference');
            $table->renameColumn('prix_unitaire', 'unit_price');
        });
    }

    public function down()
    {
        // Renommer les colonnes en français
        Schema::table('supply_supplier', function (Blueprint $table) {
            $table->renameColumn('supply_id', 'fourniture_id');
            $table->renameColumn('supplier_id', 'fournisseur_id');
            $table->renameColumn('supplier_reference', 'reference_fournisseur');
            $table->renameColumn('unit_price', 'prix_unitaire');
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->renameColumn('delivery_id', 'livraison_id');
            $table->renameColumn('location_id', 'emplacement_id');
            $table->renameColumn('supply_id', 'fourniture_id');
            $table->renameColumn('quantity', 'quantite');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->renameColumn('order_id', 'commande_id');
            $table->renameColumn('expected_date', 'date_prevue');
            $table->renameColumn('actual_date', 'date_effective');
            $table->renameColumn('status', 'statut');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('order_id', 'commande_id');
            $table->renameColumn('supply_id', 'fourniture_id');
            $table->renameColumn('quantity', 'quantite');
            $table->renameColumn('unit_price', 'prix_unitaire');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('supplier_id', 'fournisseur_id');
            $table->renameColumn('status', 'statut');
            $table->renameColumn('order_date', 'date_commande');
            $table->renameColumn('expected_delivery_date', 'date_livraison_prevue');
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->renameColumn('comment', 'commentaire');
            $table->renameColumn('processed', 'traitee');
        });

        Schema::table('stock_items', function (Blueprint $table) {
            $table->renameColumn('supply_id', 'fourniture_id');
            $table->renameColumn('location_id', 'emplacement_id');
            $table->renameColumn('estimated_quantity', 'quantite_estimee');
            $table->renameColumn('local_alert_threshold', 'seuil_alerte_local');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->renameColumn('name', 'nom');
            $table->renameColumn('plan_position', 'position_plan');
            $table->renameColumn('site_id', 'etablissement_id');
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->renameColumn('name', 'nom');
            $table->renameColumn('reference', 'reference_isfac');
            $table->renameColumn('packaging', 'conditionnement');
            $table->renameColumn('catalog_url', 'url_catalogue');
            $table->renameColumn('alert_threshold', 'seuil_alerte_global');
            $table->renameColumn('category_id', 'categorie_id');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->renameColumn('name', 'nom');
            $table->renameColumn('catalog_url', 'url_catalogue');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name', 'nom');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->renameColumn('name', 'nom');
            $table->renameColumn('address', 'adresse');
            $table->renameColumn('city', 'ville');
            $table->renameColumn('postal_code', 'code_postal');
        });

        // Renommer les tables en français
        Schema::rename('supply_supplier', 'fourniture_fournisseur');
        Schema::rename('delivery_items', 'details_livraison');
        Schema::rename('deliveries', 'livraisons');
        Schema::rename('order_items', 'lignes_commande');
        Schema::rename('orders', 'commandes');
        Schema::rename('alerts', 'alertes');
        Schema::rename('stock_items', 'stocks');
        Schema::rename('locations', 'emplacements');
        Schema::rename('supplies', 'fournitures');
        Schema::rename('suppliers', 'fournisseurs');
        Schema::rename('sites', 'etablissements');
    }
};
