<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_supply', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('supply_id')->constrained()->onDelete('cascade');
            $table->string('supplier_reference')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('catalog_url')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'supply_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_supply');
    }
};
