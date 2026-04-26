<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TypeCompanyController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CompanySelectionController;

use App\Http\Controllers\UserController;
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
    Route::resource('/services', ServiceController::class);
    Route::resource('/users', UserController::class);
    Route::resource('/companies', CompanyController::class);
    Route::resource('/type-companies', TypeCompanyController::class);
    Route::get('/select-company', [CompanySelectionController::class, 'index'])->name('company.select');
    Route::post('/select-company', [CompanySelectionController::class, 'store'])->name('company.select.store');
});

require __DIR__ . '/auth.php';
