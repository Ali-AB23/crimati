<?php

use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrgUnitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;
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

    // Le Tableau de Bord
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // La gestion du Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // === FONCTIONNALITÉS OUVERTES À TOUS LES EMPLOYÉS CONNECTÉS ===

    // Tickets (Création et suivi)
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('ticket-comments.store');


    // === FONCTIONNALITÉS RESTREINTES (Admin IT & Inventoriste) ===
    // NB: On mettra un vrai middleware de Rôle ici plus tard, 
    // pour l'instant on les protège juste par le Auth classique.

    Route::resource('assets', AssetController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('org-units', OrgUnitController::class);
    Route::resource('asset-categories', AssetCategoryController::class);
    Route::resource('asset-types', AssetTypeController::class);
});

Route::resource('assets', AssetController::class);

// Route::resource('tickets', TicketController::class);

// Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('ticket-comments.store');

// Route::resource('locations', LocationController::class);
// Route::resource('employees', EmployeeController::class);

// Route::resource('asset-categories', AssetCategoryController::class);
// Route::resource('asset-types', AssetTypeController::class);
// Route::resource('org-units', OrgUnitController::class);

// Route::resource('assets', AssetController::class);
// Route::resource('tickets', TicketController::class);

// Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('ticket-comments.store');

// Route::resource('locations', LocationController::class);
// Route::resource('employees', EmployeeController::class);

// Route::resource('asset-categories', AssetCategoryController::class);
// Route::resource('asset-types', AssetTypeController::class);
// Route::resource('org-units', OrgUnitController::class);

require __DIR__ . '/auth.php';


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');
