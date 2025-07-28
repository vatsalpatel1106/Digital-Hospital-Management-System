<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Middleware)
|--------------------------------------------------------------------------
*/

// 🔰 Welcome Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Patient Authentication
|--------------------------------------------------------------------------
*/
Route::get('/patient/register', [RegisterController::class, 'openregister'])->name('register.form');
Route::post('/patient/register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/patient/login', [RegisterController::class, 'openlogin'])->name('login.form');
Route::post('/patient/login', [RegisterController::class, 'storelogin'])->name('login.store');

Route::get('/patient/logout', [RegisterController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Doctor Authentication
|--------------------------------------------------------------------------
*/
Route::get('/doctor/login', [DoctorController::class, 'loginForm'])->name('doctor.login.form');
Route::post('/doctor/login', [DoctorController::class, 'login'])->name('doctor.login');
Route::get('/doctor/logout', [DoctorController::class, 'logout'])->name('doctor.logout');

/*
|--------------------------------------------------------------------------
| Patient Protected Routes (with Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware('patient.auth')->group(function () {
    Route::get('/patient/dashboard', [RegisterController::class, 'dashboard'])->name('patient.dashboard');

    // Appointment actions
    Route::get('/appointment/create', [AppointmentController::class, 'create'])->name('appointment.create');
Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
Route::post('/appointment/create', [AppointmentController::class, 'store'])->name('appointment.create.post');

    Route::get('/appointment/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointment.cancel');
    Route::post('/appointment/{id}/update', [AppointmentController::class, 'update'])->name('appointment.update');
    Route::get('/appointments', [AppointmentController::class, 'patientAppointments'])->name('appointment.patient.view');
});

/*
|--------------------------------------------------------------------------
| Doctor Protected Routes (with Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware('doctor.auth')->group(function () {
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');

    Route::get('/doctor/appointments', [AppointmentController::class, 'doctorAppointments'])->name('appointment.doctor.view');
    Route::get('/appointment/{id}/accept', [DoctorController::class, 'acceptAppointment'])->name('appointment.accept');
    Route::get('/appointment/{id}/reject', [DoctorController::class, 'rejectAppointment'])->name('appointment.reject');
    Route::post('/appointment/{id}/upload-medicine', [DoctorController::class, 'uploadMedicine'])->name('appointment.upload.medicine');
});
