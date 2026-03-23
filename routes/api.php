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

// Public booking API routes (no authentication required)
Route::prefix('booking')->name('booking.api.')->group(function () {
    // API para obtener información del médico/clínica
    Route::get('/{slug}/info', [BookingController::class, 'getProviderInfo'])->name('info');

    // API para obtener sucursales (si es clínica)
    Route::get('/{slug}/locations', [BookingController::class, 'getLocations'])->name('locations');

    // API para obtener médicos por sucursal y especialidad
    Route::get('/{slug}/doctors', [BookingController::class, 'getDoctors'])->name('doctors');

    // API para obtener horarios disponibles
    Route::get('/{slug}/availability', [BookingController::class, 'getAvailability'])->name('availability');

    // Crear reserva pública
    Route::post('/{slug}/reserve', [BookingController::class, 'createReservation'])->name('reserve');
});

// Authentication routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Routes for mobile API (using Sanctum tokens)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/recent-activity', [DashboardController::class, 'recentActivity']);

    // Clinics
    Route::apiResource('clinics', ClinicController::class);
    Route::get('/clinics/{clinic}/doctors', [ClinicController::class, 'doctors']);
    Route::get('/clinics/{clinic}/patients', [ClinicController::class, 'patients']);
    Route::get('/clinics/{clinic}/appointments', [ClinicController::class, 'appointments']);

    // Doctors
    Route::apiResource('doctors', DoctorController::class);
    Route::get('/doctors/{doctor}/appointments', [DoctorController::class, 'appointments']);
    Route::get('/doctors/{doctor}/today-appointments', [DoctorController::class, 'todayAppointments']);
    Route::get('/doctors/{doctor}/surgeries', [DoctorController::class, 'surgeries']);
    Route::get('/doctors/{doctor}/exams', [DoctorController::class, 'requestedExams']);
    Route::get('/doctors/{doctor}/schedules', [DoctorScheduleController::class, 'byDoctor']);

    // Patients
    Route::apiResource('patients', PatientController::class);
    Route::get('/patients/{patient}/medical-history', [PatientController::class, 'medicalHistory']);
    Route::put('/patients/{patient}/medical-history', [PatientController::class, 'updateMedicalHistory']);
    Route::get('/patients/{patient}/vital-signs', [PatientController::class, 'vitalSigns']);
    Route::post('/patients/{patient}/vital-signs', [PatientController::class, 'addVitalSigns']);
    Route::get('/patients/{patient}/appointments', [PatientController::class, 'appointments']);
    Route::get('/patients/{patient}/surgeries', [PatientController::class, 'surgeries']);
    Route::get('/patients/{patient}/exams', [PatientController::class, 'medicalExams']);
    Route::get('/patients/{patient}/invoices', [PatientController::class, 'invoices']);

    // Appointments (API only - basic CRUD)
    // IMPORTANT: Specific routes MUST come BEFORE apiResource to avoid model binding conflicts
    Route::get('appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('sanctum.appointments.available-slots');
    Route::get('appointments/check-availability', [AppointmentController::class, 'checkAvailability'])->name('sanctum.appointments.check-availability');
    Route::get('appointments/today', [AppointmentController::class, 'today'])->name('sanctum.appointments.today');
    Route::get('appointments/stats', [AppointmentController::class, 'stats'])->name('sanctum.appointments.stats');

    Route::apiResource('appointments', AppointmentController::class);
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);

    // Surgeries
    Route::apiResource('surgeries', SurgeryController::class);
    Route::get('/surgeries/today', [SurgeryController::class, 'today']);
    Route::patch('/surgeries/{surgery}/status', [SurgeryController::class, 'updateStatus']);

    // Medical Exams
    Route::apiResource('medical-exams', MedicalExamController::class);
    Route::get('/medical-exams/{exam}/result', [MedicalExamController::class, 'getResult']);
    Route::post('/medical-exams/{exam}/result', [MedicalExamController::class, 'addResult']);
    Route::put('/medical-exams/{exam}/result', [MedicalExamController::class, 'updateResult']);

    // Invoices
    Route::apiResource('invoices', InvoiceController::class);
    Route::patch('/invoices/{invoice}/payment-status', [InvoiceController::class, 'updatePaymentStatus']);
    Route::get('/invoices/overdue', [InvoiceController::class, 'overdue']);

    // Medications
    Route::apiResource('medications', MedicationController::class);
    Route::get('/medications/low-stock', [MedicationController::class, 'lowStock']);
    Route::get('/medications/expiring', [MedicationController::class, 'expiring']);
    Route::post('/medications/{medication}/movement', [MedicationController::class, 'addMovement']);
    Route::get('/medications/{medication}/movements', [MedicationController::class, 'movements']);

    // Doctor Schedules (Agenda Multi-Ubicación)
    Route::apiResource('doctor-schedules', DoctorScheduleController::class);
    Route::get('/doctor-schedules/{schedule}/available-slots', [DoctorScheduleController::class, 'availableSlots']);
    Route::get('/doctor-schedules/{schedule}/calendar', [DoctorScheduleController::class, 'calendar']);
    Route::get('/schedules/by-city/{city}', [DoctorScheduleController::class, 'byCity']);
});

// Routes for web interface (using web session authentication)
Route::middleware(['auth:web'])->group(function () {
    // Users - Basic list for filters (available to medical staff)
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/users/basic', [App\Http\Controllers\Api\UserController::class, 'basicList']);
    });

    // Appointment functionality for web interface
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('api.appointments.available-slots');
        Route::get('appointments/check-availability', [AppointmentController::class, 'checkAvailability'])->name('api.appointments.check-availability');
        Route::get('appointments/today', [AppointmentController::class, 'today'])->name('api.appointments.today');
    });

    // Payment Methods
    Route::apiResource('payment-methods', PaymentMethodController::class);
    Route::post('/payment-methods/order', [PaymentMethodController::class, 'updateOrder']);
    Route::post('/payment-methods/{paymentMethod}/generate-link', [PaymentMethodController::class, 'generateLink']);
    Route::get('/payment-methods/link/{token}', [PaymentMethodController::class, 'getPaymentLink']);
    Route::get('/payment-methods/data/for-link', [PaymentMethodController::class, 'getPaymentLinkData']);

    // Payment Links
    Route::get('/payment-links', [PaymentLinkController::class, 'getLinks']);
    Route::post('/payment-links', [PaymentLinkController::class, 'store']);
    Route::get('/payment-links/create-data', [PaymentLinkController::class, 'getCreateData']);
    Route::get('/payment-links/stats', [PaymentLinkController::class, 'getStats']);
    Route::get('/payment-links/{id}', [PaymentLinkController::class, 'show']);
    Route::patch('/payment-links/{id}/deactivate', [PaymentLinkController::class, 'deactivate']);
    Route::delete('/payment-links/{id}', [PaymentLinkController::class, 'destroy']);
});

// Public Payment Links Routes (no authentication required)
Route::get('/payment-links/{token}/info', [PaymentLinkController::class, 'getPublicInfo']);
Route::get('/payment-links/{token}/qr', [PaymentLinkController::class, 'generateQr']);
Route::post('/payment-links/{token}/process', [PaymentLinkController::class, 'processPayment']);
Route::post('/payment-links/{token}/confirm', [PaymentLinkController::class, 'confirmManualPayment']);
