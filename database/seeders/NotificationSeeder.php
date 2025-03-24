<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')->pluck('id')->toArray();

        $notifications = [
            [
                'type' => 'request',
                'title' => 'Demande de fournitures bureau',
                'message' => 'Besoin de stylos, cahiers et post-its',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'request',
                'title' => 'Demande de matériel informatique',
                'message' => 'Besoin d\'une nouvelle souris sans fil',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'request',
                'title' => 'Demande de mobilier',
                'message' => 'Besoin d\'une nouvelle chaise ergonomique',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'request',
                'title' => 'Demande de fournitures salle de cours',
                'message' => 'Besoin de marqueurs pour tableau blanc',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'request',
                'title' => 'Demande de maintenance',
                'message' => 'Problème avec le climatiseur',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'request',
                'title' => 'Demande de fournitures sanitaires',
                'message' => 'Besoin de papier toilette et savon',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'request',
                'title' => 'Demande de matériel pédagogique',
                'message' => 'Besoin de nouveaux manuels',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'request',
                'title' => 'Demande de fournitures cuisine',
                'message' => 'Besoin de vaisselle et ustensiles',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'request',
                'title' => 'Demande de matériel sportif',
                'message' => 'Besoin de nouveaux ballons',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'request',
                'title' => 'Demande de fournitures entretien',
                'message' => 'Besoin de produits de nettoyage',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($notifications as $notification) {
            $notificationId = DB::table('notifications')->insertGetId($notification);

            // Attacher un utilisateur aléatoire à la notification
            DB::table('notification_user')->insert([
                'notification_id' => $notificationId,
                'user_id' => $users[array_rand($users)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
