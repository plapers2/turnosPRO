<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TypeCompanyController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CompanySelectionController;
use App\Http\Controllers\OpeningHourController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotificationLogController;
use App\Http\Controllers\ProfileSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\PasswordChangeController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('master')) {
            return redirect()->route('master.index');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('role:admin|empleado|cliente');


// ─────────────────────────────────────────────
// Cambio de contraseña obligatorio (primer login)
// ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/change-password', [PasswordChangeController::class, 'update'])->name('password.change.update');
});


// ─────────────────────────────────────────────
// Rutas generales autenticadas
// ─────────────────────────────────────────────
Route::middleware(['auth', 'password.changed'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Horarios de empresa
    Route::resource('/opening-hours', OpeningHourController::class)->except('index')->middleware('role:admin');
    Route::post('/opening-hours/{id}/restore', [OpeningHourController::class, 'restore'])->middleware('role:admin');
    Route::get('/opening-hours', [OpeningHourController::class, 'index'])->middleware('permission:ver horarios')->name('opening-hours.index');

    // Servicios
    Route::resource('/services', ServiceController::class)->except('index')->middleware('role:admin');
    Route::post('/services/{id}/restore', [ServiceController::class, 'restore'])->name('service.restore')->middleware('role:admin');
    Route::get('/services', [ServiceController::class, 'index'])->middleware('permission:ver servicios')->name('services.index');

    // Perfil
    Route::get('/settings', [ProfileSettingsController::class, 'edit'])->name('profile.settings');
    Route::put('/settings', [ProfileSettingsController::class, 'update'])->name('profile.settings.update');
});


// ─────────────────────────────────────────────
// Panel Master
// ─────────────────────────────────────────────
Route::middleware(['auth', 'password.changed', 'role:master'])->prefix('master')->name('master.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/create', [CompanyController::class, 'create'])->name('create');
    Route::post('/', [CompanyController::class, 'store'])->name('store');
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/restore', [CompanyController::class, 'restore'])->name('restore');
    Route::post('/{company}/assign-admin', [CompanyController::class, 'assignAdmin'])->name('assign-admin');
    Route::resource('/type-companies', TypeCompanyController::class);
    Route::post('/type-companies/{id}/restore', [TypeCompanyController::class, 'restore'])->name('type-companies.restore');
});


// ─────────────────────────────────────────────
// Rutas para clientes
// ─────────────────────────────────────────────
Route::middleware(['auth', 'password.changed', 'role:cliente'])->group(function () {
    Route::get('/booking/citas-ocupadas', [BookingController::class, 'citasOcupadas']);
    Route::get('/booking/horarios-empresa', [BookingController::class, 'horariosEmpresa']);
    Route::get('/appointments', [BookingController::class, 'selectCompany'])->name('appointment.index');
    Route::get('/booking/{company}/services', [BookingController::class, 'selectServices'])->name('appointments.selectServices');
    Route::get('/booking/confirm', [BookingController::class, 'prepareCreate'])->name('booking.prepareCreate');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('appointments.store');
    Route::get('/booking/profesionales-disponibles', [BookingController::class, 'profesionalesDisponibles'])->name('booking.profesionales');
    Route::get('/mis-citas', [BookingController::class, 'misCitas'])->name('appointment.history');
    Route::get('/booking/validar-combinacion', [BookingController::class, 'validarCombinacion']);
});


// ─────────────────────────────────────────────
// Rutas exclusivas de admin
// ─────────────────────────────────────────────
Route::middleware(['auth', 'password.changed', 'role:admin'])->group(function () {
    Route::resource('/users', UserController::class);
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/appointments/export', [BookingController::class, 'exportView'])->name('appointments.export');
    Route::get('/appointments/export-pdf', [BookingController::class, 'exportPdf'])->name('appointments.export-pdf');
    Route::get('/notification-logs', [NotificationLogController::class, 'index'])->name('notification-logs.index');
});


// ─────────────────────────────────────────────
// Rutas para admin y empleado
// ─────────────────────────────────────────────
Route::middleware(['auth', 'password.changed', 'role:admin|empleado'])->group(function () {
    Route::get('/select-company', [CompanySelectionController::class, 'index'])->name('company.select');
    Route::post('/select-company', [CompanySelectionController::class, 'store'])->name('company.select.store');
    Route::get('/appointment-manager', function () {
        return view('appointment-manager.index');
    })->name('appointment-manager.index');
});


// Cancelar cita desde email (pública con token)
Route::get('/appointments/cancel/{token}', [BookingController::class, 'cancelByToken'])->name('appointments.cancel');

// ⚠️ Solo para pruebas — eliminar en producción
Route::get('/test-mail', function () {
    $appointment = \App\Models\Appointment::with(['customer', 'user', 'company', 'services'])->latest()->first();
    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentConfirmationMail($appointment));
    return 'Email enviado — revisa Mailtrap o el log';
});

require __DIR__ . '/auth.php';
