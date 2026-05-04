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
    // Perfil
    Route::get('/customer/profile/edit', [CustomerController::class, 'editProfile'])->name('customer.profile.edit');
    Route::put('/customer/profile/update', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
    
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

    // Horarios de empresa
    Route::resource('/opening-hours', OpeningHourController::class);
    Route::post('/opening-hours/{id}/restore', [OpeningHourController::class, 'restore']);

    // Clientes
    Route::resource('/customers', CustomerController::class)->except(['create']);

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

Route::middleware(['auth', 'role:admin|empleado|cliente'])->group(
    function () {
        // Manejador de citas
        Route::get('/appointment-manager', function () {
            return view('appointment-manager.index');
        })->name('appointment-manager.index');
    }
);
require __DIR__ . '/auth.php';
