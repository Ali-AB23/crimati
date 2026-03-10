<?php

use App\Http\Controllers\AssetCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrgUnitController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;

Route::get('/', function () {
    return view('welcome');
});


Route::resource('assets', AssetController::class);


Route::resource('tickets', TicketController::class);

Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('ticket-comments.store');

Route::resource('locations', LocationController::class);
Route::resource('employees', EmployeeController::class);


Route::resource('asset-categories', AssetCategoryController::class);
Route::resource('asset-types', AssetTypeController::class);
Route::resource('org-units', OrgUnitController::class);

Route::resource('assets', AssetController::class);
Route::resource('tickets', TicketController::class);

Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('ticket-comments.store');

Route::resource('locations', LocationController::class);
Route::resource('employees', EmployeeController::class);


Route::resource('asset-categories', AssetCategoryController::class);
Route::resource('asset-types', AssetTypeController::class);
Route::resource('org-units', OrgUnitController::class);