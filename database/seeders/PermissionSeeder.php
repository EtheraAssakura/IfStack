<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des permissions de base
        $permissions = [
            'read' => 'Peut lire les données',
            'edit' => 'Peut modifier les données',
            'delete' => 'Peut supprimer les données',
        ];

        foreach ($permissions as $name => $description) {
            Permission::create([
                'name' => $name,
                'description' => $description,
            ]);
        }

        // Attribution de toutes les permissions au rôle Administrateur
        $adminRole = Role::where('name', 'Administrateur')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::all());
        }

        // Attribution de la permission de lecture au rôle Utilisateur
        $userRole = Role::where('name', 'Utilisateur')->first();
        if ($userRole) {
            $userRole->permissions()->sync(Permission::where('name', 'read')->get());
        }
    }
}
