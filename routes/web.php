<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\QrScanController;
use App\Http\Controllers\AlertController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;


// Routes d'authentification
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('welcome');

    // Route pour la prise de fournitures
    Route::get('/stock/take', [StockItemController::class, 'take'])->name('stock-items.take');
    Route::post('/stock/take/{stockItem}', [StockItemController::class, 'takeStock'])->name('stock-items.take.submit');

    // Routes API pour les sites
    Route::get('/api/sites', [SiteController::class, 'apiIndex'])->name('api.sites.index');
    Route::get('/api/sites/{site}', [SiteController::class, 'apiShow'])->name('api.sites.show');

    // Route pour la création de notifications Utilisateur
    Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    // Routes des stocks
    Route::get('/stocks', [StockItemController::class, 'index'])->name('stock-items.index');
    Route::get('/stocks/sites', [StockItemController::class, 'sites'])->name('stock-items.sites');
    Route::get('/stocks/by-site', [StockItemController::class, 'stocksBySite'])->name('stock-items.by-site');
    Route::get('/stocks/create', [StockItemController::class, 'create'])->name('stock-items.create');
    Route::post('/stocks', [StockItemController::class, 'store'])->name('stock-items.store');
    Route::get('/stocks/{stockItem}', [StockItemController::class, 'show'])->name('stock-items.show');
    Route::get('/stocks/{stockItem}/edit', [StockItemController::class, 'edit'])->name('stock-items.edit');
    Route::put('/stocks/{stockItem}', [StockItemController::class, 'update'])->name('stock-items.update');
    Route::get('/stocks/scan/{qrCode}', [QrScanController::class, 'scan'])->name('stock-items.scan');
    Route::post('/stocks/{stockItem}/signal-rupture', [StockItemController::class, 'signalRupture'])->name('stock-items.signal-rupture');

    // Routes des fournitures
    Route::get('/fournitures', [SupplyController::class, 'index'])->name('supplies.index');
    Route::get('/fournitures/create', [SupplyController::class, 'create'])->name('supplies.create');
    Route::post('/fournitures', [SupplyController::class, 'store'])->name('supplies.store');
    Route::get('/fournitures/{supply}', [SupplyController::class, 'show'])->name('supplies.show');
    Route::get('/fournitures/{supply}/edit', [SupplyController::class, 'edit'])->name('supplies.edit');
    Route::put('/fournitures/{supply}', [SupplyController::class, 'update'])->name('supplies.update');
    Route::post('/fournitures/{supply}', [SupplyController::class, 'update'])->name('supplies.update');
    Route::delete('/fournitures/{supply}', [SupplyController::class, 'destroy'])->name('supplies.destroy');
    Route::delete('/fournitures/image/{supply}', [SupplyController::class, 'removeImage'])->name('supplies.remove-image');

    // Livraisons
    Route::get('/livraisons', [DeliveryController::class, 'index'])->name('livraisons.index');
    Route::get('/livraisons/calendar', [DeliveryController::class, 'calendar'])->name('livraisons.calendar');
    Route::get('/livraisons/create/{commande}', [DeliveryController::class, 'create'])->name('livraisons.create');
    Route::post('/livraisons/{commande}', [DeliveryController::class, 'store'])->name('livraisons.store');
    Route::get('/livraisons/{livraison}', [DeliveryController::class, 'show'])->name('livraisons.show');
    Route::post('/livraisons/{livraison}/effectuer', [DeliveryController::class, 'effectuer'])->name('livraisons.effectuer');
    Route::get('/livraisons/{livraison}/bon', [DeliveryController::class, 'generateBonLivraison'])->name('livraisons.bon');

    // Établissements
    Route::get('/etablissements', [SiteController::class, 'index'])->name('sites.index');
    Route::get('/etablissements/create', [SiteController::class, 'create'])->name('sites.create');
    Route::post('/etablissements', [SiteController::class, 'store'])->name('sites.store');
    Route::get('/etablissements/{site}', [SiteController::class, 'show'])->name('sites.show');
    Route::get('/etablissements/{site}/edit', [SiteController::class, 'edit'])->name('sites.edit');
    Route::put('/etablissements/{site}', [SiteController::class, 'update'])->name('sites.update');
    Route::post('/etablissements/{site}', [SiteController::class, 'update'])->name('sites.update');
    Route::delete('/etablissements/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');
    Route::delete('/etablissements/{site}/plan', [SiteController::class, 'removePlan'])->name('sites.remove-plan');

    // Emplacements dans les établissements
    Route::post('/etablissements/{site:id}/locations', [LocationController::class, 'store'])->name('sites.locations.store');
    Route::get('/etablissements/{site:id}/locations/{location:id}', [LocationController::class, 'show'])->name('sites.locations.show');
    Route::put('/etablissements/{site:id}/locations/{location:id}', [LocationController::class, 'update'])->name('sites.locations.update');
    Route::post('/etablissements/{site:id}/locations/{location:id}', [LocationController::class, 'update'])->name('sites.locations.update');
    Route::delete('/etablissements/{site:id}/locations/{location:id}', [LocationController::class, 'destroy'])->name('sites.locations.destroy');
    Route::post('/etablissements/{site:id}/locations/{location:id}/upload-photo', [LocationController::class, 'uploadPhoto'])->name('sites.locations.upload-photo');

    Route::put('/notifications/{id}/process', [NotificationController::class, 'process'])->name('notifications.process');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');

    // Routes des notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/archive', [NotificationController::class, 'archive'])->name('notifications.archive');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');

    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::get('orders/{order}/export', [OrderController::class, 'exportExcel'])->name('orders.export');
    Route::post('orders/{order}/validate', [OrderController::class, 'validate'])->name('orders.validate');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('orders/suggest', [OrderController::class, 'suggestOrders'])->name('orders.suggest');

    // Route d'export des stocks
    Route::get('/stocks/export/{site?}', [StockItemController::class, 'export'])->name('stocks.export');
});

// Routes d'erreur
Route::get('/404', [ErrorController::class, 'notFound'])->name('errors.404');
Route::get('/403', [ErrorController::class, 'forbidden'])->name('errors.403');
Route::get('/419', [ErrorController::class, 'expired'])->name('errors.419');
Route::get('/429', [ErrorController::class, 'tooManyRequests'])->name('errors.429');
Route::get('/500', [ErrorController::class, 'serverError'])->name('errors.500');
Route::get('/503', [ErrorController::class, 'serviceUnavailable'])->name('errors.503');

require __DIR__ . '/settings.php';
