<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table pour les rapports générés
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'stock', 'orders', 'deliveries', 'usage'
            $table->json('parameters'); // Paramètres utilisés pour générer le rapport
            $table->json('data'); // Données du rapport
            $table->foreignId('user_id')->constrained(); // Qui a généré le rapport
            $table->foreignId('site_id')->nullable()->constrained(); // Pour quel site (null si global)
            $table->timestamp('generated_at');
            $table->timestamps();
        });

        // Table pour les statistiques d'utilisation
        Schema::create('usage_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained();
            $table->foreignId('site_id')->constrained();
            $table->integer('quantity_used');
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();
        });

        // Table pour les analyses prévisionnelles
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained();
            $table->foreignId('site_id')->constrained();
            $table->integer('predicted_quantity');
            $table->decimal('predicted_cost', 10, 2);
            $table->date('prediction_date');
            $table->date('target_date');
            $table->json('calculation_parameters'); // Paramètres utilisés pour le calcul
            $table->timestamps();
        });

        // Table pour les indicateurs de performance (KPIs)
        Schema::create('kpi_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // 'stock', 'orders', 'deliveries', 'costs'
            $table->decimal('value', 15, 2);
            $table->string('unit'); // %, €, jours, etc.
            $table->foreignId('site_id')->nullable()->constrained();
            $table->date('measurement_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_metrics');
        Schema::dropIfExists('forecasts');
        Schema::dropIfExists('usage_statistics');
        Schema::dropIfExists('reports');
    }
};
