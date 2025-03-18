<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Mise à jour des slugs pour les sites existants
        DB::table('sites')->get()->each(function ($site) {
            $slug = match (strtolower($site->name)) {
                'poitiers' => 'poitiers',
                'la rochelle' => 'la-rochelle',
                'niort' => 'niort',
                default => Str::slug($site->name),
            };

            DB::table('sites')
                ->where('id', $site->id)
                ->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        // Rien à faire ici car nous ne voulons pas annuler la mise à jour des slugs
    }
};
