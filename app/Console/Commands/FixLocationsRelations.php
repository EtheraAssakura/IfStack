<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Etablissement;
use Illuminate\Console\Command;

class FixLocationsRelations extends Command
{
    protected $signature = 'locations:fix-relations';
    protected $description = 'Vérifie et corrige les relations des emplacements';

    public function handle()
    {
        $this->info('Vérification des relations des emplacements...');

        $locations = Location::all();
        $count = 0;

        foreach ($locations as $location) {
            $this->info("Vérification de l'emplacement #{$location->id} ({$location->name})");

            // Vérifier si l'établissement existe
            if (!$location->etablissement_id) {
                if ($location->site_id) {
                    $etablissement = Etablissement::where('site_id', $location->site_id)->first();
                    if ($etablissement) {
                        $location->etablissement_id = $etablissement->id;
                        $location->save();
                        $count++;
                        $this->info("- Établissement #{$etablissement->id} associé");
                    } else {
                        $this->error("- Aucun établissement trouvé pour le site #{$location->site_id}");
                    }
                } else {
                    $this->error("- Pas de site_id défini");
                }
            } else {
                $this->info("- Établissement #{$location->etablissement_id} déjà associé");
            }

            // Vérifier si le site correspond à l'établissement
            if ($location->etablissement_id && $location->site_id) {
                $etablissement = Etablissement::find($location->etablissement_id);
                if ($etablissement && $etablissement->site_id !== $location->site_id) {
                    $location->site_id = $etablissement->site_id;
                    $location->save();
                    $count++;
                    $this->info("- Site corrigé pour correspondre à l'établissement");
                }
            }
        }

        $this->info("\n{$count} corrections effectuées");
    }
}
