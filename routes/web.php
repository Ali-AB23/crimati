<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OrgUnitController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetMovementController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\TicketCategoryController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Routes Sécurisées (Utilisateurs connectés)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // ====================================================================
    // 🟢🔵🔴 NIVEAU 1 : ACCÈS COMMUN (ADMIN + INVENTORISTE + EMPLOYE)
    // ====================================================================

    // Le Tableau de Bord
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // La gestion du Profil (Chacun peut modifier son propre mot de passe)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //materiel
    Route::get('assets',[AssetController::class, 'index'])->name('assets.index');
    Route::get('assets/{asset}/show',[AssetController::class, 'show'])->name('assets.show');

    // Tickets (Limité à la consultation et création pour tout le monde)
    // Les règles fines (statut, assignation, due_at) se font dans TicketController
    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);;
    Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('ticket-comments.store');


    // --- NOTIFICATIONS ---
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read',[App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/clear-read', [App\Http\Controllers\NotificationController::class, 'clearRead'])->name('notifications.clearRead');
    Route::get('/notifications/{notification}/read/{ticket}', [App\Http\Controllers\NotificationController::class, 'readAndRedirect'])->name('notifications.readAndRedirect');

    // ====================================================================
    // 🟢🔵 NIVEAU 2 : ACCÈS GESTION DE PARC (ADMIN + INVENTORISTE)
    // ====================================================================

    Route::middleware(['role:ADMIN_IT,INVENTORISTE'])->group(function () {
        

        // Le catalogue physique
        Route::get('assets/create',[AssetController::class, 'create'])->name('assets.create');
        Route::post('assets',[AssetController::class, 'store'])->name('assets.store');
        Route::get('assets/{asset}/edit',[AssetController::class, 'edit'])->name('assets.edit');
        
        Route::put('assets/{asset}',[AssetController::class, 'update'])->name('assets.update');
        Route::delete('assets/{asset}',[AssetController::class, 'destroy'])->name('assets.destroy');
        Route::post('assets/{asset}/move', [AssetController::class, 'move'])->name('assets.move');




        // Route::resource('assets', AssetController::class);
        // Historique global (Admin & Inventoriste)
        Route::get('movements',[AssetMovementController::class, 'index'])->name('movements.index');
        // TODO: On ajoutera ici les routes pour l'historique (Movements) et l'Import Excel : 
        /*
            Route::resource('movements', AssetMovementController::class)->only(['index', 'show']);
        
        */
        // --- IMPORT EXCEL ---
        Route::get('/import/assets',[App\Http\Controllers\ImportController::class, 'showUploadForm'])->name('import.upload');
        Route::post('/import/assets/process', [App\Http\Controllers\ImportController::class, 'processUpload'])->name('import.process');
        Route::get('/import/assets/result',[App\Http\Controllers\ImportController::class, 'showResultForm'])->name('import.result');
    });


    // ====================================================================
    // 🟢 NIVEAU 3 : ACCÈS SUPER-ADMIN (ADMIN_IT UNIQUEMENT)
    // ====================================================================

    Route::middleware(['role:ADMIN_IT'])->group(function () {

        // ✨  Traitement des tickets réservé à l'Admin IT :  Lui seul peut modifier (assigner, statut, date limite) et supprimer un ticket.
        Route::resource('tickets', TicketController::class)->only(['edit', 'update', 'destroy']);
       
        // Gestion des acteurs
        Route::resource('employees', EmployeeController::class);
        Route::resource('users', UserController::class);

        Route::post('users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');


        // Référentiels
        Route::resource('locations', LocationController::class);
        Route::resource('org-units', OrgUnitController::class);
        Route::resource('asset-categories', AssetCategoryController::class);
        Route::resource('asset-types', AssetTypeController::class);
        Route::resource('ticket-categories', TicketCategoryController::class);

    });
});

// Routes d'authentification (Breeze)
require __DIR__ . '/auth.php';



