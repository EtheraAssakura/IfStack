<?php

use App\Http\Controllers\CommandeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FournitureController;
use App\Http\Controllers\LivraisonController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\EmplacementController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Routes d'authentification
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        $user = Auth::user();
        $locationId = request()->query('location');

        if (!$locationId && $user && $user->site_id) {
            $location = \App\Models\Location::where('site_id', $user->site_id)->first();
            if ($location) {
                $locationId = $location->id;
            }
        }

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'locationId' => $locationId,
        ]);
    })->name('welcome');

    // Route pour la prise de fournitures
    Route::get('/stock/take', [StockController::class, 'take'])->name('stock.take');
    Route::post('/stock/take/{stock}', [StockController::class, 'takeStock'])->name('stock.take.submit');

    // Route pour la création de demandes
    Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes des stocks
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/sites', [StockController::class, 'sites'])->name('stocks.sites');
    Route::get('/stocks/by-site', [StockController::class, 'stocksBySite'])->name('stocks.by-site');
    Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');
    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::get('/stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');
    Route::get('/stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    Route::put('/stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
    Route::post('/stocks/{stock}/signal-rupture', [StockController::class, 'signalRupture'])->name('stocks.signal-rupture');
    Route::get('/stocks/scan/{qrCode}', [StockController::class, 'scanQr'])->name('stocks.scan');
});

// Routes des fournitures
Route::get('/fournitures', [FournitureController::class, 'index'])->name('fournitures.index');
Route::get('/fournitures/create', [FournitureController::class, 'create'])->name('fournitures.create');
Route::post('/fournitures', [FournitureController::class, 'store'])->name('fournitures.store');
Route::get('/fournitures/{fourniture}', [FournitureController::class, 'show'])->name('fournitures.show');
Route::get('/fournitures/{fourniture}/edit', [FournitureController::class, 'edit'])->name('fournitures.edit');
Route::put('/fournitures/{fourniture}', [FournitureController::class, 'update'])->name('fournitures.update');
Route::post('/fournitures/{fourniture}', [FournitureController::class, 'update'])->name('fournitures.update');
Route::delete('/fournitures/{fourniture}', [FournitureController::class, 'destroy'])->name('fournitures.destroy');

// Commandes
Route::resource('commandes', CommandeController::class);
Route::get('/commandes/{commande}/excel', [CommandeController::class, 'exportExcel'])->name('commandes.excel');
Route::post('/commandes/{commande}/valider', [CommandeController::class, 'valider'])->name('commandes.valider');
Route::post('/commandes/{commande}/annuler', [CommandeController::class, 'annuler'])->name('commandes.annuler');

// Livraisons
Route::get('/livraisons', [LivraisonController::class, 'index'])->name('livraisons.index');
Route::get('/livraisons/calendar', [LivraisonController::class, 'calendar'])->name('livraisons.calendar');
Route::get('/livraisons/create/{commande}', [LivraisonController::class, 'create'])->name('livraisons.create');
Route::post('/livraisons/{commande}', [LivraisonController::class, 'store'])->name('livraisons.store');
Route::get('/livraisons/{livraison}', [LivraisonController::class, 'show'])->name('livraisons.show');
Route::post('/livraisons/{livraison}/effectuer', [LivraisonController::class, 'effectuer'])->name('livraisons.effectuer');
Route::get('/livraisons/{livraison}/bon', [LivraisonController::class, 'generateBonLivraison'])->name('livraisons.bon');

// Établissements
Route::get('/etablissements', [EtablissementController::class, 'index'])->name('etablissements.index');
Route::get('/etablissements/create', [EtablissementController::class, 'create'])->name('etablissements.create');
Route::post('/etablissements', [EtablissementController::class, 'store'])->name('etablissements.store');
Route::get('/etablissements/{etablissement}', [EtablissementController::class, 'show'])->name('etablissements.show');
Route::get('/etablissements/{etablissement}/edit', [EtablissementController::class, 'edit'])->name('etablissements.edit');
Route::put('/etablissements/{etablissement}', [EtablissementController::class, 'update'])->name('etablissements.update');
Route::post('/etablissements/{etablissement}', [EtablissementController::class, 'update'])->name('etablissements.update');
Route::delete('/etablissements/{etablissement}', [EtablissementController::class, 'destroy'])->name('etablissements.destroy');

// Emplacements dans les établissements
Route::post('/etablissements/{etablissement}/locations', [LocationController::class, 'store'])->name('etablissements.locations.store');
Route::get('/etablissements/{etablissement}/locations/{location}', [LocationController::class, 'show'])->name('etablissements.locations.show');
Route::put('/etablissements/{etablissement}/locations/{location}', [LocationController::class, 'update'])->name('etablissements.locations.update');
Route::post('/etablissements/{etablissement}/locations/{location}', [LocationController::class, 'update'])->name('etablissements.locations.update');
Route::delete('/etablissements/{etablissement}/locations/{location}', [LocationController::class, 'destroy'])->name('etablissements.locations.destroy');
Route::post('/etablissements/{etablissement}/locations/{location}/upload-photo', [LocationController::class, 'uploadPhoto'])->name('etablissements.locations.upload-photo');

// Gestion des utilisateurs (restreint aux administrateurs)
Route::get('/utilisateurs', [UserController::class, 'index'])->name('utilisateurs.index');
Route::get('/utilisateurs/create', [UserController::class, 'create'])->name('utilisateurs.create');
Route::post('/utilisateurs', [UserController::class, 'store'])->name('utilisateurs.store');
Route::get('/utilisateurs/{user}/edit', [UserController::class, 'edit'])->name('utilisateurs.edit');
Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('utilisateurs.update');
Route::delete('/utilisateurs/{user}', [UserController::class, 'destroy'])->name('utilisateurs.destroy');
Route::post('/utilisateurs/{user}/role', [UserController::class, 'updateRole'])->name('utilisateurs.role');

// Notifications
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');

// Rapports
Route::get('/rapports/consommation', [RapportController::class, 'consommation'])->name('rapports.consommation');
Route::get('/rapports/top-produits', [RapportController::class, 'topProduits'])->name('rapports.top-produits');
Route::get('/rapports/alertes', [RapportController::class, 'alertes'])->name('rapports.alertes');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('categories', CategoryController::class);
});

// Order routes
Route::middleware(['auth'])->group(function () {
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::get('orders/{order}/export', [OrderController::class, 'exportExcel'])->name('orders.export');
    Route::post('orders/{order}/validate', [OrderController::class, 'validate'])->name('orders.validate');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('orders/suggest', [OrderController::class, 'suggestOrders'])->name('orders.suggest');

    // Route d'export des stocks
    Route::get('/stocks/export/{site?}', [StockController::class, 'export'])->name('stocks.export');
});

require __DIR__ . '/settings.php';
