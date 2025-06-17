<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\SurgeryController;
use App\Http\Controllers\Api\MedicationController;
use App\Http\Controllers\Api\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});

// Web Authentication Routes
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();
        
        return response()->json([
            'message' => 'Login successful',
            'user' => Auth::user()
        ]);
    }

    return response()->json([
        'message' => 'Invalid credentials'
    ], 401);
})->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return response()->json(['message' => 'Logged out successfully']);
})->name('logout');

// API routes accessible via web authentication
Route::middleware('auth:web')->group(function () {
    Route::get('/api/user', function () {
        return response()->json(Auth::user());
    })->name('api.user');
    
    Route::get('/api/doctors', [DoctorController::class, 'index']);
    Route::get('/api/patients', [PatientController::class, 'index']);
    Route::get('/api/appointments', [AppointmentController::class, 'index']);
    Route::get('/api/surgeries', [SurgeryController::class, 'index']);
    Route::get('/api/medications', [MedicationController::class, 'index']);
    Route::get('/api/invoices', [InvoiceController::class, 'index']);
});
