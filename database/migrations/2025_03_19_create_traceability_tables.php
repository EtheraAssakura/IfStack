<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table pour l'historique des mouvements de stock
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('type'); // 'in', 'out', 'transfer', 'adjustment'
            $table->integer('quantity');
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            $table->string('reason')->nullable();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->foreignId('delivery_id')->nullable()->constrained();
            $table->timestamps();
        });

        // Table pour le journal des actions
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('action'); // 'create', 'update', 'delete', 'view'
            $table->string('entity_type'); // 'supply', 'order', 'delivery', etc.
            $table->unsignedBigInteger('entity_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // Table pour les journaux d'erreurs et d'exceptions
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('error_type');
            $table->text('message');
            $table->text('stack_trace')->nullable();
            $table->json('context')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // Table pour l'historique des scans QR
        Schema::create('qr_scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('location_id')->constrained();
            $table->string('action_type'); // 'inventory', 'movement', 'verification'
            $table->json('scanned_data');
            $table->string('device_info')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_scan_logs');
        Schema::dropIfExists('error_logs');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('stock_movements');
    }
};
