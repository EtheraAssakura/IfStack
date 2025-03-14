<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('sites', 'slug')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->string('slug')->after('name');
            });
        }

        // Mise à jour des slugs pour les sites existants
        DB::table('sites')->get()->each(function ($site) {
            $slug = match (strtolower($site->name)) {
                'isfac poitiers' => 'isfac-poitiers',
                'isfac la rochelle' => 'isfac-la-rochelle',
                'isfac niort' => 'isfac-niort',
                default => 'isfac-' . Str::slug($site->name),
            };

            DB::table('sites')
                ->where('id', $site->id)
                ->update(['slug' => $slug]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sites', 'slug')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};
