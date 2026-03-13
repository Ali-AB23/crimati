<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OrgUnitController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetTypeController;
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


    // Tickets (Limité à la consultation et création pour tout le monde)
    // Les règles fines (statut, assignation, due_at) se font dans TicketController
    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);;
    Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('ticket-comments.store');

    // ====================================================================
    // 🟢🔵 NIVEAU 2 : ACCÈS GESTION DE PARC (ADMIN + INVENTORISTE)
    // ====================================================================

    Route::middleware(['role:ADMIN_IT,INVENTORISTE'])->group(function () {

        // Le catalogue physique
        Route::resource('assets', AssetController::class);
        Route::post('assets/{asset}/move', [App\Http\Controllers\AssetController::class, 'move'])->name('assets.move');
        // TODO: On ajoutera ici les routes pour l'historique (Movements) et l'Import Excel : 
        /*
            Route::resource('movements', AssetMovementController::class)->only(['index', 'show']);
        
        // Import Excel
        Route::get('/import/assets',[ImportController::class, 'create'])->name('import.assets.create');
        Route::post('/import/assets', [ImportController::class, 'store'])->name('import.assets.store');
        */
    });


    // ====================================================================
    // 🟢 NIVEAU 3 : ACCÈS SUPER-ADMIN (ADMIN_IT UNIQUEMENT)
    // ====================================================================

    Route::middleware(['role:ADMIN_IT'])->group(function () {

        // ✨  Traitement des tickets réservé à l'Admin IT :  Lui seul peut modifier (assigner, statut, date limite) et supprimer un ticket.
        Route::resource('tickets', TicketController::class)->only(['edit', 'update', 'destroy']);
       
        // Gestion des acteurs
        Route::resource('employees', EmployeeController::class);
        // TODO: Route::resource('users', UserController::class);

        // Référentiels
        Route::resource('locations', LocationController::class);
        Route::resource('org-units', OrgUnitController::class);
        Route::resource('asset-categories', AssetCategoryController::class);
        Route::resource('asset-types', AssetTypeController::class);
        // TODO: Route::resource('ticket-categories', TicketCategoryController::class);

    });
});

// Routes d'authentification (Breeze)
require __DIR__ . '/auth.php';


//this is a temporary route mouvements
Route::get('/movements', function () { return 'Mouvements'; })->name('movements.index');
Route::get('/ticket-categories', function () { return 'ticket-categories'; })->name('ticket-categories.index');
Route::get('/users', function () { return 'users'; })->name('users.index');

