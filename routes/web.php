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
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Routes d'authentification
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/rapport-hebdomadaire', [DashboardController::class, 'generateWeeklyReport'])->name('rapport.hebdomadaire');

    // Fournitures
    Route::resource('fournitures', FournitureController::class);

    // Stocks
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');
    Route::get('/stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    Route::put('/stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
    Route::post('/stocks/{stock}/rupture', [StockController::class, 'signalRupture'])->name('stocks.rupture');
    Route::get('/stocks/scan/{qrCode}', [StockController::class, 'scanQr'])->name('stocks.scan');
    Route::get('/stocks/export/csv', [StockController::class, 'export'])->name('stocks.export');

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

    // Emplacements
    Route::get('/emplacements', [EmplacementController::class, 'index'])->name('emplacements.index');
    Route::get('/emplacements/create', [EmplacementController::class, 'create'])->name('emplacements.create');
    Route::post('/emplacements', [EmplacementController::class, 'store'])->name('emplacements.store');
    Route::get('/emplacements/{emplacement}', [EmplacementController::class, 'show'])->name('emplacements.show');
    Route::get('/emplacements/{emplacement}/edit', [EmplacementController::class, 'edit'])->name('emplacements.edit');
    Route::put('/emplacements/{emplacement}', [EmplacementController::class, 'update'])->name('emplacements.update');
    Route::delete('/emplacements/{emplacement}', [EmplacementController::class, 'destroy'])->name('emplacements.destroy');
    Route::post('/emplacements/{emplacement}/upload-photo', [EmplacementController::class, 'uploadPhoto'])->name('emplacements.upload-photo');

    // Établissements
    Route::get('/etablissements', [EtablissementController::class, 'index'])->name('etablissements.index');
    Route::get('/etablissements/create', [EtablissementController::class, 'create'])->name('etablissements.create');
    Route::post('/etablissements', [EtablissementController::class, 'store'])->name('etablissements.store');
    Route::get('/etablissements/{etablissement}', [EtablissementController::class, 'show'])->name('etablissements.show');
    Route::get('/etablissements/{etablissement}/edit', [EtablissementController::class, 'edit'])->name('etablissements.edit');
    Route::put('/etablissements/{etablissement}', [EtablissementController::class, 'update'])->name('etablissements.update');
    Route::delete('/etablissements/{etablissement}', [EtablissementController::class, 'destroy'])->name('etablissements.destroy');
    Route::post('/etablissements/{etablissement}/upload-plan', [EtablissementController::class, 'uploadPlan'])->name('etablissements.upload-plan');

    // Gestion des utilisateurs (restreint aux administrateurs)
    Route::middleware(['admin'])->group(function () {
        Route::get('/utilisateurs', [UserController::class, 'index'])->name('utilisateurs.index');
        Route::get('/utilisateurs/create', [UserController::class, 'create'])->name('utilisateurs.create');
        Route::post('/utilisateurs', [UserController::class, 'store'])->name('utilisateurs.store');
        Route::get('/utilisateurs/{user}/edit', [UserController::class, 'edit'])->name('utilisateurs.edit');
        Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('utilisateurs.update');
        Route::delete('/utilisateurs/{user}', [UserController::class, 'destroy'])->name('utilisateurs.destroy');
        Route::post('/utilisateurs/{user}/role', [UserController::class, 'updateRole'])->name('utilisateurs.role');
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');

    // Rapports
    Route::get('/rapports/consommation', [RapportController::class, 'consommation'])->name('rapports.consommation');
    Route::get('/rapports/top-produits', [RapportController::class, 'topProduits'])->name('rapports.top-produits');
    Route::get('/rapports/alertes', [RapportController::class, 'alertes'])->name('rapports.alertes');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Routes pour la gestion des stocks
    Route::get('/stocks', function () {
        return Inertia::render('Stock/Index');
    })->name('stocks.index');
});

require __DIR__ . '/settings.php';
