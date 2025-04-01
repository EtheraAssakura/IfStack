<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Sites table (doit être créée en premier car elle est référencée par users)
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('plan_path')->nullable();
            $table->boolean('is_headquarters')->default(false);
            $table->string('slug')->unique();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('permissions');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Role Permission pivot table
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('site_id')->nullable()->constrained('sites');
            $table->rememberToken();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Role User pivot table
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Suppliers table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('catalog_url')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Supplies table
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reference')->unique();
            $table->string('packaging');
            $table->string('image_url')->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Locations table
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('photo_url')->nullable();
            $table->json('plan_position')->nullable();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Stock items table
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations');
            $table->integer('estimated_quantity');
            $table->integer('local_alert_threshold')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Stock movements table
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items');
            $table->foreignId('user_id')->constrained('users');
            $table->string('type'); // 'in', 'out'
            $table->integer('quantity');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Alerts table
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('stock_item_id')->constrained('stock_items');
            $table->foreignId('user_id')->constrained('users');
            $table->string('type');
            $table->text('comment')->nullable();
            $table->boolean('processed')->default(false);
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Orders table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('user_id')->constrained('users');
            $table->string('status');
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Order items table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('supply_id')->constrained('supplies');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Deliveries table
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('user_id')->constrained('users');
            $table->date('expected_date');
            $table->date('actual_date')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Delivery items table
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('supply_id')->constrained('supplies');
            $table->integer('quantity');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // QR Scans table
        Schema::create('qr_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('location_id')->constrained('locations');
            $table->timestamp('scanned_at');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('type');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Notification User pivot table
        Schema::create('notification_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        // Supplier Supply pivot table
        Schema::create('supplier_supply', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('supplier_reference');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('catalog_url')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }

    public function down()
    {
        // Supprimer d'abord les tables pivot et les tables qui dépendent d'autres tables
        Schema::dropIfExists('supplier_supply');
        Schema::dropIfExists('notification_user');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('qr_scans');
        Schema::dropIfExists('delivery_items');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('supplies');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('users');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('sites');
    }
};
