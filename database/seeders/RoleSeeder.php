<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'Administrateur',
                'description' => 'Accès complet à toutes les fonctionnalités du système',
                'permissions' => json_encode([
                    'supplies' => ['view', 'create', 'edit', 'delete'],
                    'orders' => ['view', 'create', 'edit', 'delete'],
                    'deliveries' => ['view', 'create', 'edit', 'delete'],
                    'stocks' => ['view', 'edit'],
                    'alerts' => ['view', 'process'],
                    'reports' => ['view', 'generate'],
                    'users' => ['view', 'create', 'edit', 'delete'],
                    'settings' => ['view', 'edit']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Utilisateur',
                'description' => 'Accès limité aux fonctionnalités de base',
                'permissions' => json_encode([
                    'supplies' => ['view'],
                    'orders' => ['view'],
                    'deliveries' => ['view'],
                    'stocks' => ['view'],
                    'alerts' => ['view', 'create'],
                    'reports' => ['view']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
