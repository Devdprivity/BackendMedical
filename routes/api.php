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
use App\Http\Controllers\Api\DashboardController;

// Authentication routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

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

    // Appointments
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('/appointments/today', [AppointmentController::class, 'today']);
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
});
