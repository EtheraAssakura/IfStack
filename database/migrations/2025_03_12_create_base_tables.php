<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    // Table des établissements
    Schema::create('etablissements', function (Blueprint $table) {
      $table->id();
      $table->string('nom');
      $table->string('adresse');
      $table->string('ville');
      $table->string('code_postal');
      $table->timestamps();
    });

    // Table des catégories de fournitures
    Schema::create('categories', function (Blueprint $table) {
      $table->id();
      $table->string('nom');
      $table->string('description')->nullable();
      $table->timestamps();
    });

    // Table des fournisseurs
    Schema::create('fournisseurs', function (Blueprint $table) {
      $table->id();
      $table->string('nom');
      $table->string('url_catalogue')->nullable();
      $table->timestamps();
    });

    // Table des fournitures
    Schema::create('fournitures', function (Blueprint $table) {
      $table->id();
      $table->string('nom');
      $table->string('reference_isfac')->unique();
      $table->string('conditionnement');
      $table->string('url_catalogue')->nullable();
      $table->integer('seuil_alerte_global');
      $table->string('image_url')->nullable();
      $table->foreignId('categorie_id')->constrained('categories');
      $table->timestamps();
    });

    // Table de liaison fournitures-fournisseurs
    Schema::create('fourniture_fournisseur', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fourniture_id')->constrained('fournitures');
      $table->foreignId('fournisseur_id')->constrained('fournisseurs');
      $table->string('reference_fournisseur');
      $table->decimal('prix_unitaire', 10, 2)->nullable();
      $table->timestamps();
    });

    // Table des emplacements
    Schema::create('emplacements', function (Blueprint $table) {
      $table->id();
      $table->string('nom');
      $table->string('description')->nullable();
      $table->string('qr_code')->unique();
      $table->string('photo_url')->nullable();
      $table->json('position_plan')->nullable(); // Stockage des coordonnées sur le plan
      $table->foreignId('etablissement_id')->constrained('etablissements');
      $table->timestamps();
    });

    // Table des stocks par emplacement
    Schema::create('stocks', function (Blueprint $table) {
      $table->id();
      $table->foreignId('fourniture_id')->constrained('fournitures');
      $table->foreignId('emplacement_id')->constrained('emplacements');
      $table->integer('quantite_estimee');
      $table->integer('seuil_alerte_local')->nullable();
      $table->timestamps();
    });

    // Table des alertes de stock
    Schema::create('alertes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('stock_id')->constrained('stocks');
      $table->foreignId('user_id')->constrained('users');
      $table->string('type'); // 'rupture', 'seuil_atteint'
      $table->text('commentaire')->nullable();
      $table->boolean('traitee')->default(false);
      $table->timestamps();
    });

    // Table des commandes
    Schema::create('commandes', function (Blueprint $table) {
      $table->id();
      $table->string('reference');
      $table->foreignId('fournisseur_id')->constrained('fournisseurs');
      $table->foreignId('user_id')->constrained('users'); // Qui a passé la commande
      $table->string('statut'); // 'en_cours', 'validee', 'livree'
      $table->date('date_commande');
      $table->date('date_livraison_prevue')->nullable();
      $table->timestamps();
    });

    // Table des lignes de commande
    Schema::create('lignes_commande', function (Blueprint $table) {
      $table->id();
      $table->foreignId('commande_id')->constrained('commandes');
      $table->foreignId('fourniture_id')->constrained('fournitures');
      $table->integer('quantite');
      $table->decimal('prix_unitaire', 10, 2);
      $table->timestamps();
    });

    // Table des livraisons
    Schema::create('livraisons', function (Blueprint $table) {
      $table->id();
      $table->foreignId('commande_id')->constrained('commandes');
      $table->foreignId('user_id')->constrained('users'); // Qui effectue la livraison
      $table->date('date_prevue');
      $table->date('date_effective')->nullable();
      $table->string('statut'); // 'planifiee', 'en_cours', 'effectuee'
      $table->timestamps();
    });

    // Table des détails de livraison
    Schema::create('details_livraison', function (Blueprint $table) {
      $table->id();
      $table->foreignId('livraison_id')->constrained('livraisons');
      $table->foreignId('emplacement_id')->constrained('emplacements');
      $table->foreignId('fourniture_id')->constrained('fournitures');
      $table->integer('quantite');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('details_livraison');
    Schema::dropIfExists('livraisons');
    Schema::dropIfExists('lignes_commande');
    Schema::dropIfExists('commandes');
    Schema::dropIfExists('alertes');
    Schema::dropIfExists('stocks');
    Schema::dropIfExists('emplacements');
    Schema::dropIfExists('fourniture_fournisseur');
    Schema::dropIfExists('fournitures');
    Schema::dropIfExists('fournisseurs');
    Schema::dropIfExists('categories');
    Schema::dropIfExists('etablissements');
  }
};
