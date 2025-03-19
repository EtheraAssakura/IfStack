<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'Administrateur')->value('id');
        $userRoleId = DB::table('roles')->where('name', 'Utilisateur')->value('id');

        // Insérer les utilisateurs
        $users = [
            [
                'name' => 'Céline DUPUIS',
                'email' => 'celine.dupuis@isfac.fr',
                'password' => Hash::make('password'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC Poitiers')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Utilisateur Niort',
                'email' => 'user.niort@isfac.fr',
                'password' => Hash::make('password'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC Niort')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Utilisateur La Rochelle',
                'email' => 'user.larochelle@isfac.fr',
                'password' => Hash::make('password'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC La Rochelle')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Ethera',
                'email' => 'etheraassakura@gmail.com',
                'password' => Hash::make('password'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC Poitiers')->value('id'),
            ],
        ];

        foreach ($users as $userData) {
            $userId = DB::table('users')->insertGetId($userData);

            // Attribuer le rôle approprié
            DB::table('user_role')->insert([
                'user_id' => $userId,
                'role_id' => $userData['email'] === 'celine.dupuis@isfac.fr' ? $adminRoleId : $userRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
