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
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route("dashboard");
});


// Rutas publicas o con restricciones
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Horarios de empresa
    Route::resource('/opening-hours', OpeningHourController::class)->except('index')->middleware('role:admin');
    Route::post('/opening-hours/{id}/restore', [OpeningHourController::class, 'restore'])->middleware('role:admin');
    Route::get('/opening-hours', [OpeningHourController::class, 'index'])->middleware('permission:ver horarios')->name('opening-hours.index');

    // Servicios
    Route::resource('/services', ServiceController::class)->except('index')->middleware('role:admin');
    Route::post('/services/{id}/restore', [ServiceController::class, 'restore'])->name('service.restore')->middleware('role:admin');
    Route::get('/services', [ServiceController::class, 'index'])->middleware('permission:ver servicios')->name('services.index');

    // Empresas
    Route::resource('/companies', CompanyController::class)->except('index')->middleware('role:admin');
    Route::get('/companies', [CompanyController::class, 'index'])->middleware('permission:ver empresas')->name('companies.index');

    // Perfil
    Route::get('/settings', [ProfileSettingsController::class, 'edit'])->name('profile.settings');
    Route::put('/settings', [ProfileSettingsController::class, 'update'])->name('profile.settings.update');
});

// Rutas para clientes
Route::middleware(['auth', 'role:cliente'])->group(function () {
    // Citas
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

// Rutas exclusivas de admin
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Usuarios
    Route::resource('/users', UserController::class);
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

    // Tipos de empresas
    Route::resource('/type-companies', TypeCompanyController::class);

    // Clientes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/index', [CustomerController::class, 'history'])->name('customers.history');

    // Exportar citas PDF
    Route::get('/appointments/export', [BookingController::class, 'exportView'])->name('appointments.export');
    Route::get('/appointments/export-pdf', [BookingController::class, 'exportPdf'])->name('appointments.export-pdf');

    // Notificaciones
    Route::get('/notification-logs', [NotificationLogController::class, 'index'])->name('notification-logs.index');
});

// Ruta para cancelar cita desde email
Route::get('/appointments/cancel/{token}', [BookingController::class, 'cancelByToken'])->name('appointments.cancel');

// ⚠️ Solo para pruebas — eliminar en producción
Route::get('/test-mail', function () {
    $appointment = \App\Models\Appointment::with(['customer', 'user', 'company', 'services'])->latest()->first();

    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentConfirmationMail($appointment));

    return 'Email enviado — revisa Mailtrap o el log';
});

// Rutas para admin y empleado
Route::middleware(['auth', 'role:admin|empleado'])->group(function () {

    // Seleccionar empresa
    Route::get('/select-company', [CompanySelectionController::class, 'index'])->name('company.select');
    Route::post('/select-company', [CompanySelectionController::class, 'store'])->name('company.select.store');

    // Manejador de citas
    Route::get('/appointment-manager', function () {
        return view('appointment-manager.index');
    })->name('appointment-manager.index');
});

require __DIR__ . '/auth.php';
