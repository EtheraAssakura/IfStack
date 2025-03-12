<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qr_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('user_id')->constrained('users');
            $table->string('scan_type'); // 'inventory', 'alert'
            $table->json('scan_data')->nullable(); // Pour stocker les données d'inventaire si nécessaire
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('qr_scans');
    }
};
