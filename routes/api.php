<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\SurgeryController;
use App\Http\Controllers\Api\MedicalExamController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\MedicationController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DoctorScheduleController;
use App\Http\Controllers\PaymentLinkController;
use App\Http\Controllers\BookingController;

// ─────────────────────────────────────────────────────────────────────────────
// PUBLIC: Booking API (no authentication required)
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('booking')->name('booking.api.')->group(function () {
    Route::get('/{slug}/info',         [BookingController::class, 'getProviderInfo'])->name('info');
    Route::get('/{slug}/locations',    [BookingController::class, 'getLocations'])->name('locations');
    Route::get('/{slug}/doctors',      [BookingController::class, 'getDoctors'])->name('doctors');
    Route::get('/{slug}/availability', [BookingController::class, 'getAvailability'])->name('availability');
    Route::post('/{slug}/reserve',     [BookingController::class, 'createReservation'])->name('reserve');
});

// ─────────────────────────────────────────────────────────────────────────────
// PUBLIC: Authentication
// ─────────────────────────────────────────────────────────────────────────────
Route::post('/auth/login',    [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// ─────────────────────────────────────────────────────────────────────────────
// MOBILE API: Sanctum token authentication
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user',    [AuthController::class, 'user']);

    // Dashboard
    Route::get('/dashboard/stats',           [DashboardController::class, 'stats']);
    Route::get('/dashboard/recent-activity', [DashboardController::class, 'recentActivity']);

    // Clinics
    Route::apiResource('clinics', ClinicController::class);
    Route::get('/clinics/{clinic}/doctors',      [ClinicController::class, 'doctors']);
    Route::get('/clinics/{clinic}/patients',     [ClinicController::class, 'patients']);
    Route::get('/clinics/{clinic}/appointments', [ClinicController::class, 'appointments']);

    // Doctors
    Route::apiResource('doctors', DoctorController::class);
    Route::get('/doctors/{doctor}/appointments',       [DoctorController::class, 'appointments']);
    Route::get('/doctors/{doctor}/today-appointments', [DoctorController::class, 'todayAppointments']);
    Route::get('/doctors/{doctor}/surgeries',          [DoctorController::class, 'surgeries']);
    Route::get('/doctors/{doctor}/exams',              [DoctorController::class, 'requestedExams']);
    Route::get('/doctors/{doctor}/schedules',          [DoctorScheduleController::class, 'byDoctor']);

    // Patients
    Route::apiResource('patients', PatientController::class);
    Route::get('/patients/{patient}/medical-history',  [PatientController::class, 'medicalHistory']);
    Route::put('/patients/{patient}/medical-history',  [PatientController::class, 'updateMedicalHistory']);
    Route::get('/patients/{patient}/vital-signs',      [PatientController::class, 'vitalSigns']);
    Route::post('/patients/{patient}/vital-signs',     [PatientController::class, 'addVitalSigns']);
    Route::get('/patients/{patient}/appointments',     [PatientController::class, 'appointments']);
    Route::get('/patients/{patient}/surgeries',        [PatientController::class, 'surgeries']);
    Route::get('/patients/{patient}/exams',            [PatientController::class, 'medicalExams']);
    Route::get('/patients/{patient}/invoices',         [PatientController::class, 'invoices']);

    // Appointments — static routes BEFORE apiResource to avoid wildcard conflicts
    Route::get('appointments/available-slots',   [AppointmentController::class, 'getAvailableSlots'])->name('sanctum.appointments.available-slots');
    Route::get('appointments/check-availability',[AppointmentController::class, 'checkAvailability'])->name('sanctum.appointments.check-availability');
    Route::get('appointments/today',             [AppointmentController::class, 'today'])->name('sanctum.appointments.today');
    Route::get('appointments/stats',             [AppointmentController::class, 'stats'])->name('sanctum.appointments.stats');
    Route::apiResource('appointments', AppointmentController::class);
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);

    // Surgeries — static routes BEFORE apiResource
    Route::get('/surgeries/today', [SurgeryController::class, 'today']);
    Route::patch('/surgeries/{surgery}/status', [SurgeryController::class, 'updateStatus']);
    Route::apiResource('surgeries', SurgeryController::class);

    // Medical Exams
    Route::apiResource('medical-exams', MedicalExamController::class);
    Route::get('/medical-exams/{exam}/result',  [MedicalExamController::class, 'getResult']);
    Route::post('/medical-exams/{exam}/result', [MedicalExamController::class, 'addResult']);
    Route::put('/medical-exams/{exam}/result',  [MedicalExamController::class, 'updateResult']);

    // Invoices — static routes BEFORE apiResource
    Route::get('/invoices/overdue', [InvoiceController::class, 'overdue']);
    Route::apiResource('invoices', InvoiceController::class);
    Route::patch('/invoices/{invoice}/payment-status', [InvoiceController::class, 'updatePaymentStatus']);

    // Medications — static routes BEFORE apiResource
    Route::get('/medications/low-stock', [MedicationController::class, 'lowStock']);
    Route::get('/medications/expiring',  [MedicationController::class, 'expiring']);
    Route::apiResource('medications', MedicationController::class);
    Route::post('/medications/{medication}/movement',  [MedicationController::class, 'addMovement']);
    Route::get('/medications/{medication}/movements',  [MedicationController::class, 'movements']);

    // Doctor Schedules
    Route::apiResource('doctor-schedules', DoctorScheduleController::class);
    Route::get('/doctor-schedules/{schedule}/available-slots', [DoctorScheduleController::class, 'availableSlots']);
    Route::get('/doctor-schedules/{schedule}/calendar',        [DoctorScheduleController::class, 'calendar']);
    Route::get('/schedules/by-city/{city}',                    [DoctorScheduleController::class, 'byCity']);
});

// ─────────────────────────────────────────────────────────────────────────────
// WEB SESSION: Routes used by browser frontend (auth:web + StartSession)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth:web'])->group(function () {

    // Users basic list for filters/dropdowns
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/users/basic', [App\Http\Controllers\Api\UserController::class, 'basicList']);
    });

    // Payment Methods utility routes (order, link generation)
    Route::post('/payment-methods/order',                         [PaymentMethodController::class, 'updateOrder']);
    Route::get('/payment-methods/data/for-link',                  [PaymentMethodController::class, 'getPaymentLinkData']);
    Route::get('/payment-methods/link/{token}',                   [PaymentMethodController::class, 'getPaymentLink']);
    Route::post('/payment-methods/{paymentMethod}/generate-link', [PaymentMethodController::class, 'generateLink']);
});
