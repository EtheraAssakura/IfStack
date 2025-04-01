<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Administrateur')->first();
        $userRole = Role::where('name', 'Utilisateur')->first();

        // Insérer les utilisateurs
        $users = [
            [
                'name' => 'Céline DUPUIS',
                'email' => 'celine.dupuis@isfac.fr',
                'password' => Hash::make('*FbY4?34r2uNw{'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC Poitiers')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Utilisateur Niort',
                'email' => 'user.niort@isfac.fr',
                'password' => Hash::make('2Yh3K-dLiz4%8^'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC Niort')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Utilisateur La Rochelle',
                'email' => 'user.larochelle@isfac.fr',
                'password' => Hash::make('FEw/8x;74cLa)5'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC La Rochelle')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Ethera',
                'email' => 'etheraassakura@gmail.com',
                'password' => Hash::make('FK9y6=33d]Fqv:'),
                'site_id' => DB::table('sites')->where('name', 'ISFAC Poitiers')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insérer les utilisateurs
        foreach ($users as $userData) {
            $user = User::create($userData);

            // Associer les rôles aux utilisateurs
            $role = $userData['email'] === 'celine.dupuis@isfac.fr' || $userData['email'] === 'etheraassakura@gmail.com'
                ? $adminRole
                : $userRole;

            $user->roles()->attach($role);
        }
    }
}
