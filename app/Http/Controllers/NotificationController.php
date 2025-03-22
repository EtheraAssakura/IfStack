<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Alert;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        // Récupérer les alertes de stock
        $latestAlerts = Alert::with(['stock.fourniture', 'user'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'type' => 'alert',
                    'title' => 'Alerte Stock - ' . $alert->stock->fourniture->name,
                    'message' => $alert->comment,
                    'is_read' => $alert->processed ?? false,
                    'created_at' => $alert->created_at,
                    'users' => [
                        [
                            'id' => $alert->user->id,
                            'name' => $alert->user->name,
                            'email' => $alert->user->email,
                            'site' => [
                                'name' => $alert->user->site->name ?? 'N/A'
                            ]
                        ]
                    ]
                ];
            });

        // Récupérer les demandes
        $latestRequests = Notification::latestRequests()->get();

        return Inertia::render('Notifications/Index', [
            'latestAlerts' => $latestAlerts,
            'latestRequests' => $latestRequests
        ]);
    }

    public function create(Notification $notification)
    {
        return Inertia::render('Notifications/Create', [
            'notification' => $notification
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $user = $request->user();
        $site = $user->site;

        $notification = Notification::create([
            'type' => 'request',
            'title' => $validated['title'],
            'message' => $validated['description'],
            'is_read' => false,
        ]);

        // Attacher l'utilisateur sans stocker ses informations
        $notification->users()->attach($user->id);

        return redirect()->route('welcome')
            ->with('success', 'Votre demande a été envoyée avec succès.');
    }

    public function show(Request $request, $id)
    {
        // Récupérer le type depuis la requête
        $type = $request->query('type');

        if ($type === 'alert') {
            // Chercher une alerte
            $alert = Alert::with(['stock.fourniture', 'user.site'])->find($id);

            if (!$alert) {
                abort(404);
            }

            $alert->update(['processed' => true]);

            // Transformer l'alerte dans le même format que les notifications
            $formattedAlert = [
                'id' => $alert->id,
                'type' => 'alert',
                'title' => 'Alerte Stock - ' . $alert->stock->fourniture->name,
                'message' => $alert->comment,
                'is_read' => $alert->processed,
                'created_at' => $alert->created_at,
                'users' => [
                    [
                        'id' => $alert->user->id,
                        'name' => $alert->user->name,
                        'email' => $alert->user->email,
                        'site' => [
                            'name' => $alert->user->site->name ?? 'N/A'
                        ]
                    ]
                ],
                'stock' => [
                    'name' => $alert->stock->fourniture->name,
                    'estimated_quantity' => $alert->stock->estimated_quantity,
                    'local_alert_threshold' => $alert->stock->local_alert_threshold
                ]
            ];

            return Inertia::render('Notifications/Show', [
                'notification' => $formattedAlert
            ]);
        } else {
            // Chercher une notification de type demande
            $notification = Notification::with('users.site')->find($id);

            if (!$notification) {
                abort(404);
            }

            $notification->update(['is_read' => true]);
            return Inertia::render('Notifications/Show', [
                'notification' => $notification
            ]);
        }
    }
}
