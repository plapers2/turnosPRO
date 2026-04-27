<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TypeCompanyController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CompanySelectionController;
use App\Http\Controllers\OpeningHourController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CostumerController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

Route::get('/', function () {
    return redirect()->route("dashboard");
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Servicios
    Route::resource('/services', ServiceController::class);
    Route::post('/services/{id}/restore', [ServiceController::class, 'restore'])->name('service.restore');

    // Usuarios
    Route::resource('/users', UserController::class);
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

    // Empresas
    Route::resource('/companies', CompanyController::class);

    // Tipos de empresas
    Route::resource('/type-companies', TypeCompanyController::class);

    // Seleccionar empresa
    Route::get('/select-company', [CompanySelectionController::class, 'index'])->name('company.select');
    Route::post('/select-company', [CompanySelectionController::class, 'store'])->name('company.select.store');

    Route::resource('/opening-hours', OpeningHourController::class);
    Route::post('/opening-hours/{id}/restore', [OpeningHourController::class, 'restore']);

    // Clientes
    Route::resource('/costumers', CostumerController::class)->except(['create']);
});

require __DIR__ . '/auth.php';
