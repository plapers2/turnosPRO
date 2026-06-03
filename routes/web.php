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
use App\Http\Controllers\ProfessionalAvailabilityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TwoFactorSetupController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ClientesMasterController;
use App\Livewire\Dashboard;
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
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ─────────────────────────────────────────────
// Cambio de contraseña obligatorio (primer login)
// ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/change-password', [PasswordChangeController::class, 'update'])->name('password.change.update');
    Route::get('/profile/two-factor', [TwoFactorSetupController::class, 'index'])
        ->name('two-factor.setup');
    // Agrega esta:
    Route::post('/user/confirmed-two-factor-authentication', [TwoFactorSetupController::class, 'confirm'])
        ->name('two-factor.confirm');
});


// ─────────────────────────────────────────────
// Rutas generales autenticadas
// ─────────────────────────────────────────────
Route::middleware(['auth', 'password.changed'])->group(function () {

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

    // Comprobante PDF de cita
    Route::get('/appointments/{id}/voucher', [BookingController::class, 'voucher'])
        ->name('appointment.voucher')
        ->middleware(['auth']);

    Route::get('/booking/citas-ocupadas', [BookingController::class, 'citasOcupadas'])
        ->middleware('role:cliente|admin|empleado');
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
    Route::get('/admins', [AdminController::class, 'index'])->name('admins.index');
    Route::get('/admins/create', [AdminController::class, 'create'])->name('admins.create');
    Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
    Route::delete('/admins/{admin}', [AdminController::class, 'destroy'])->name('admins.destroy');
    Route::post('/admins/{id}/restore', [AdminController::class, 'restore'])->name('admins.restore');
    Route::get('/clientes', [ClientesMasterController::class, 'index'])->name('clientes.index');
    Route::patch('/clientes/{user}/toggle-plan', [ClientesMasterController::class, 'togglePlan'])->name('clientes.toggle-plan');
});


// ─────────────────────────────────────────────
// Rutas para clientes
// ─────────────────────────────────────────────
Route::middleware(['auth', 'password.changed', 'role:cliente'])->group(function () {
    Route::get('/booking/horarios-empresa', [BookingController::class, 'horariosEmpresa']);
    Route::get('/appointments', [BookingController::class, 'selectCompany'])->name('appointment.index');
    Route::get('/booking/{company}/services', [BookingController::class, 'selectServices'])->name('appointments.selectServices');
    Route::get('/booking/confirm', [BookingController::class, 'prepareCreate'])->name('booking.prepareCreate');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('appointments.store');
    Route::get('/booking/profesionales-disponibles', [BookingController::class, 'profesionalesDisponibles'])->name('booking.profesionales');
    Route::get('/my-appointments', [BookingController::class, 'misCitas'])->name('appointment.history');
    Route::post('/my-appointments/cancel/{id}', [BookingController::class, 'cancelFromPanel'])->name('appointments.cancelFromPanel');
    Route::get('/booking/validar-combinacion', [BookingController::class, 'validarCombinacion']);
    Route::get('/appointments/new', [BookingController::class, 'unified'])->name('appointment.create');
    Route::get('/booking/servicios-empresa', [BookingController::class, 'serviciosEmpresa'])->name('booking.serviciosEmpresa');
    Route::get('/appointments/create', function () {
        return view('appointment.create');
    })->name('appointment.create');
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
    Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
    Route::post('/invitations/{id}/restore', [InvitationController::class, 'restore'])->name('invitations.restore');
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
    Route::get('/professional-availability', [ProfessionalAvailabilityController::class, 'index'])->name('professional-availability.index');
    Route::get('/appointments/new-for-client', function () {
        return view('employee.appointment-create');
    })->name('employee.appointment.create');
});


// ─────────────────────────────────────────────
// Rutas publicas
// ─────────────────────────────────────────────

// Cancelar cita desde email
Route::get('/appointments/cancel/{token}', [BookingController::class, 'cancelByToken'])->name('appointments.cancel');
Route::post('/appointments/cancel/{token}', [BookingController::class, 'cancelByTokenConfirm'])->name('appointments.cancel.confirm');

// Registro con invitación
Route::get('/register/{token}', [RegisteredUserController::class, 'create'])->middleware('guest')->name('register.invite');

// Aceptar invitación estando autenticado
Route::get('/invitations/{token}/accept', [InvitationController::class, 'accept'])->middleware('auth')->name('invitations.accept');

// Registro libre (sin invitación)
Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest')->name('register');

// Prueba todos los emails de una vez
Route::get('/test-all-mails', function () {
    $appointment = \App\Models\Appointment::with(['customer', 'user', 'company', 'services'])->latest()->first();
    $admin = \App\Models\User::role('admin')->first();
    $company = $appointment->company;

    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentConfirmationMail($appointment));
    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentAutoConfirmedMail($appointment));
    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentCancelledAdminMail($appointment));
    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentCancelledByEmployeeMail($appointment));
    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentCompletedMail($appointment));
    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentConfirmedByEmployeeMail($appointment));
    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentReminderMail($appointment, '24h'));
    \Mail::to('test@test.com')->send(new \App\Mail\AppointmentReminderMail($appointment, '1h'));
    \Mail::to('test@test.com')->send(new \App\Mail\AdminCredentialsMail($admin, null, 'TempPass123'));
    \Mail::to('test@test.com')->send(new \App\Mail\AdminCompanyAssignedMail($admin, $company));

    return 'Todos los emails enviados — revisa Mailtrap';
});

require __DIR__ . '/auth.php';
