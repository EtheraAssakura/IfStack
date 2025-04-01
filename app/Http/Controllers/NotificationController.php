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
        // Récupérer les alertes de stock (traitées et non traitées)
        $latestAlerts = Alert::with(['stockItem.supply', 'user'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'type' => 'alert',
                'title' => $alert->title,
                'comment' => $alert->comment,
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

        // Récupérer les demandes (traitées et non traitées)
        $latestRequests = Notification::where('type', 'request')
            ->latest()
            ->take(10)
            ->with(['users' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.site_id')
                    ->with(['site' => function ($q) {
                        $q->select('sites.id', 'sites.name');
                    }]);
            }])
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'type' => $request->type,
                    'title' => $request->title,
                'content' => $request->content,
                    'is_read' => $request->is_read,
                    'created_at' => $request->created_at,
                    'users' => $request->users->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'site' => [
                                'name' => $user->site->name ?? 'N/A'
                            ]
                        ];
                    })->toArray()
                ];
            });

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
            'content' => 'required|string',
        ]);

        $user = $request->user();
        $site = $user->site;

        $notification = Notification::create([
            'type' => 'request',
            'title' => $validated['title'],
            'content' => $validated['content'],
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
            $alert = Alert::with(['stockItem.supply', 'stockItem.location', 'user.site'])->find($id);

            if (!$alert) {
                abort(404);
            }

            // Transformer l'alerte dans le même format que les notifications
            $formattedAlert = [
                'id' => $alert->id,
                'type' => 'alert',
                'title' => $alert->title,
                'content' => $alert->comment,
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
                    'name' => $alert->stockItem->supply->name,
                    'estimated_quantity' => $alert->stockItem->estimated_quantity,
                    'local_alert_threshold' => $alert->stockItem->local_alert_threshold,
                    'processed' => $alert->processed,
                    'location' => [
                        'name' => $alert->stockItem->location->name,
                        'site' => [
                            'name' => $alert->stockItem->location->site->name ?? 'N/A'
                        ]
                    ]
                ]
            ];

            return Inertia::render('Notifications/Show', [
                'notification' => $formattedAlert
            ]);
        } else {
            // Chercher une notification
            $notification = Notification::with('users.site')->find($id);

            if (!$notification) {
                abort(404);
            }

            return Inertia::render('Notifications/Show', [
                'notification' => $notification
            ]);
        }
    }

    public function process(Request $request, $id)
    {
        $type = $request->query('type');
        $processed = $request->boolean('processed');

        if ($type === 'alert') {
            $alert = Alert::findOrFail($id);
            $alert->update(['processed' => $processed]);
        } else {
            $notification = Notification::findOrFail($id);
            $notification->update(['is_read' => $processed]);
        }

        return back();
    }

    public function archive(Request $request)
    {
        $type = $request->query('type');

        if ($type === 'alert') {
            $alerts = Alert::with(['stockItem.supply', 'user'])
                ->where('processed', true)
                ->latest()
                ->paginate(10)
                ->map(function ($alert) {
                    return [
                        'id' => $alert->id,
                        'type' => 'alert',
                        'title' => $alert->title,
                        'comment' => $alert->comment,
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
                        ]
                    ];
                });

            return Inertia::render('Notifications/Archive', [
                'notifications' => $alerts,
                'type' => 'alert'
            ]);
        } else {
            $notifications = Notification::where('type', 'request')
                ->where('is_read', true)
                ->latest()
                ->with(['users' => function ($query) {
                    $query->select('users.id', 'users.name', 'users.email', 'users.site_id')
                        ->with(['site' => function ($q) {
                            $q->select('sites.id', 'sites.name');
                        }]);
                }])
                ->paginate(10)
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'type' => $request->type,
                        'title' => $request->title,
                        'content' => $request->content,
                        'is_read' => $request->is_read,
                        'created_at' => $request->created_at,
                        'users' => $request->users->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'site' => [
                                    'name' => $user->site->name ?? 'N/A'
                                ]
                            ];
                        })->toArray()
                    ];
                });

            return Inertia::render('Notifications/Archive', [
                'notifications' => $notifications,
                'type' => 'request'
            ]);
        }
    }
}
