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
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\MedicalExamController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Api\TreatmentController;
use App\Http\Controllers\Api\DoctorPatientRelationshipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\AppointmentPaymentController;
use App\Http\Controllers\OnboardingController;

// Página de inicio
Route::get('/', function () {
    return view('welcome');
});

// Documentación de la API
Route::get('/documentation', function () {
    return view('documentation');
})->name('documentation');

// Vista de Login
Route::get('/login', function () {
    return view('pages.auth.login');
})->name('login.view');

// Google OAuth Routes
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Onboarding Routes
Route::middleware(['auth'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('index');
    Route::get('/profile', [OnboardingController::class, 'profile'])->name('profile');
    Route::post('/profile', [OnboardingController::class, 'updateProfile'])->name('profile.update');
    Route::get('/schedule', [OnboardingController::class, 'schedule'])->name('schedule');
    Route::post('/schedule', [OnboardingController::class, 'updateSchedule'])->name('schedule.update');
    Route::get('/booking', [OnboardingController::class, 'booking'])->name('booking');
    Route::post('/booking', [OnboardingController::class, 'enableBooking'])->name('booking.enable');
    Route::get('/payments', [OnboardingController::class, 'payments'])->name('payments');
    Route::post('/payments', [OnboardingController::class, 'updatePayments'])->name('payments.update');
    Route::get('/complete', [OnboardingController::class, 'complete'])->name('complete');
    Route::post('/finish', [OnboardingController::class, 'finish'])->name('finish');
    Route::post('/skip', [OnboardingController::class, 'skip'])->name('skip');
});

// Debug route (remove in production)
Route::get('/debug/google-config', function () {
    return response()->json([
        'app_url' => config('app.url'),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
        'google_client_id' => config('services.google.client_id') ? 'Set' : 'Not set',
        'google_client_secret' => config('services.google.client_secret') ? 'Set' : 'Not set',
        'google_redirect' => config('services.google.redirect'),
        'environment' => app()->environment(),
        'current_url' => request()->getSchemeAndHttpHost(),
    ]);
})->name('debug.google-config');

// Registration Route
Route::post('/auth/register', [GoogleController::class, 'register'])->name('auth.register');

// Subscription Plans (public)
Route::get('/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
Route::get('/api/plans', [SubscriptionController::class, 'getPlans'])->name('api.plans');

// Dashboard (requiere autenticación)
Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.index');
    })->name('dashboard');
    
    // Subscription Management Routes
    Route::get('/subscription', [SubscriptionController::class, 'dashboard'])->name('subscription.dashboard');
    Route::get('/api/subscription/status', [SubscriptionController::class, 'status'])->name('api.subscription.status');
    Route::get('/api/subscription/usage', [SubscriptionController::class, 'usage'])->name('api.subscription.usage');
    Route::post('/api/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('api.subscription.subscribe');
    Route::post('/api/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('api.subscription.cancel');
    Route::post('/api/subscription/reactivate', [SubscriptionController::class, 'reactivate'])->name('api.subscription.reactivate');
    Route::get('/api/subscription/billing-history', [SubscriptionController::class, 'billingHistory'])->name('api.subscription.billing-history');
    Route::post('/api/subscription/check-limit', [SubscriptionController::class, 'checkLimit'])->name('api.subscription.check-limit');
    
    // Rutas de Pacientes - Acceso por roles
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/patients', function () {
            return view('pages.patients.index');
        })->name('patients.index');
        
        Route::middleware(['subscription.limits:add_patient'])->group(function () {
            Route::get('/patients/create', function () {
                return view('pages.patients.create');
            })->name('patients.create');
        });
        
        Route::get('/patients/{id}', function ($id) {
            return view('pages.patients.show', compact('id'));
        })->name('patients.show');
    });
    
    // Rutas de Doctores - Solo admin puede gestionar doctores
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/doctors', function () {
            return view('pages.doctors.index');
        })->name('doctors.index');
        
        Route::get('/doctors/{id}', function ($id) {
            return view('pages.doctors.show', compact('id'));
        })->name('doctors.show');
    });
    
    Route::middleware(['role:admin', 'subscription.limits:add_doctor'])->group(function () {
        Route::get('/doctors/create', function () {
            return view('pages.doctors.create');
        })->name('doctors.create');
    });
    
    // Rutas de Citas - Acceso por roles
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/appointments', function () {
            return view('pages.appointments.index');
        })->name('appointments.index');
    });
    
    Route::middleware(['role:admin,doctor,nurse,receptionist', 'subscription.limits:add_appointment'])->group(function () {
        Route::get('/appointments/create', function () {
            return view('pages.appointments.create');
        })->name('appointments.create');
    });
    
    // Rutas de Cirugías - Solo doctores y admin
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::get('/surgeries', function () {
            return view('pages.surgeries.index');
        })->name('surgeries.index');
        
        Route::get('/surgeries/create', function () {
            return view('pages.surgeries.create');
        })->name('surgeries.create');
    });
    
    // Rutas de Medicamentos - Acceso por roles y suscripción
    Route::middleware(['role:admin,doctor,nurse', 'subscription.feature:inventory_management'])->group(function () {
        Route::get('/medications', function () {
            return view('pages.medications.index');
        })->name('medications.index');
        
        Route::get('/medications/create', function () {
            return view('pages.medications.create');
        })->name('medications.create');
    });
    
    // Rutas de Facturas - Admin y contabilidad
    Route::middleware(['role:admin,accountant'])->group(function () {
        Route::get('/invoices', function () {
            return view('pages.invoices.index');
        })->name('invoices.index');
        
        Route::get('/invoices/create', function () {
            return view('pages.invoices.create');
        })->name('invoices.create');
    });
    
    // Rutas de Clínicas - Solo admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/clinics', function () {
            return view('pages.clinics.index');
        })->name('clinics.index');
    });
    
    Route::middleware(['role:admin', 'subscription.limits:add_location'])->group(function () {
        Route::get('/clinics/create', function () {
            return view('pages.clinics.create');
        })->name('clinics.create');
    });
    
    // Rutas de Exámenes - Acceso por roles
    Route::middleware(['role:admin,doctor,lab_technician'])->group(function () {
        Route::get('/exams', function () {
            return view('pages.exams.index');
        })->name('exams.index');
        
        Route::get('/exams/{id}', function ($id) {
            return view('pages.exams.show', compact('id'));
        })->name('exams.show');
        
        Route::get('/exams/create', function () {
            return view('pages.exams.create');
        })->name('exams.create');
    });
    
    // Rutas de Métodos de Pago - Doctores y admin
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::get('/payment-methods', function () {
            return view('pages.payment-methods.index');
        })->name('payment-methods.index');
        
        Route::get('/payment-methods/create', function () {
            return view('pages.payment-methods.create');
        })->name('payment-methods.create');
        
        Route::get('/payment-methods/{id}', function ($id) {
            return view('pages.payment-methods.show', compact('id'));
        })->name('payment-methods.show');
        
        Route::get('/payment-methods/{id}/edit', function ($id) {
            return view('pages.payment-methods.edit', compact('id'));
        })->name('payment-methods.edit');
    });
    
    // Rutas de Administración de Usuarios - Solo admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', function () {
            return view('pages.users.index');
        })->name('users.index');
        
        Route::get('/users/create', function () {
            return view('pages.users.create');
        })->name('users.create');
        
        Route::get('/users/{id}', function ($id) {
            return view('pages.users.show', compact('id'));
        })->name('users.show');
    });
    
    // Profile routes - Todos los usuarios autenticados
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/settings', [App\Http\Controllers\ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('/profile/schedule', [App\Http\Controllers\ProfileController::class, 'schedule'])->name('profile.schedule');
    Route::post('/profile/schedule', [App\Http\Controllers\ProfileController::class, 'updateSchedule'])->name('profile.update-schedule');
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

// Debug routes (NO AUTH REQUIRED) - for testing server status only
Route::get('/api/debug/server-status', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now(),
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION
    ]);
});

// Enhanced authentication check route
Route::get('/api/auth/check', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'role' => auth()->user()->role,
        ] : null,
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'timestamp' => now()
    ]);
});

// API routes with REAL DATABASE CONNECTIONS
Route::middleware('auth:web')->group(function () {
    Route::get('/api/user', function () {
        return response()->json(Auth::user());
    })->name('api.user');
    
    // Test route for debugging authentication
    Route::get('/api/test-auth', function () {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->user(),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token()
        ]);
    })->name('api.test-auth');
    
    // Dashboard API routes - REAL DATA
    Route::get('/api/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/api/dashboard/recent-activity', [DashboardController::class, 'recentActivity']);
    Route::get('/api/dashboard/debug', [DashboardController::class, 'debug']);
    
    // Stats routes for each module
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/patients/stats', [PatientController::class, 'stats']);
    });
    
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/appointments/stats', [AppointmentController::class, 'stats']);
    });
    
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::get('/api/surgeries/stats', [SurgeryController::class, 'stats']);
    });
    
    Route::middleware(['role:admin,doctor,lab_technician'])->group(function () {
        Route::get('/api/exams/stats', [MedicalExamController::class, 'stats']);
    });
    
    Route::middleware(['role:admin,doctor,nurse', 'subscription.feature:inventory_management'])->group(function () {
        Route::get('/api/medications/stats', [MedicationController::class, 'stats']);
    });
    
    Route::middleware(['role:admin,accountant'])->group(function () {
        Route::get('/api/invoices/stats', [InvoiceController::class, 'stats']);
    });
    
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/api/clinics/stats', [ClinicController::class, 'stats']);
        Route::get('/api/doctors/stats', [DoctorController::class, 'stats']);
    });
    
    // Patients API - Role-based access
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/patients', [PatientController::class, 'index']);
        Route::get('/api/patients/{patient}', [PatientController::class, 'show']);
        Route::put('/api/patients/{patient}', [PatientController::class, 'update']);
        
        // Patient related data
        Route::get('/api/patients/{patient}/appointments', [PatientController::class, 'appointments']);
    });
    
    Route::middleware(['role:admin,doctor,nurse,receptionist', 'subscription.limits:add_patient'])->group(function () {
        Route::post('/api/patients', [PatientController::class, 'store']);
    });
    
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::delete('/api/patients/{patient}', [PatientController::class, 'destroy']);
    });
    
    // Doctors API - Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/api/doctors', [DoctorController::class, 'index']);
        Route::get('/api/doctors/{doctor}', [DoctorController::class, 'show']);
        Route::put('/api/doctors/{doctor}', [DoctorController::class, 'update']);
        Route::delete('/api/doctors/{doctor}', [DoctorController::class, 'destroy']);
    });
    
    Route::middleware(['role:admin', 'subscription.limits:add_doctor'])->group(function () {
        Route::post('/api/doctors', [DoctorController::class, 'store']);
    });
    
    // Basic doctors list for filters (available to all medical staff)
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/doctors/list', [DoctorController::class, 'basicList']);
    });
    
    // Appointments API - Role-based access
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/appointments', [AppointmentController::class, 'index']);
        Route::get('/api/appointments/{appointment}', [AppointmentController::class, 'show']);
        Route::put('/api/appointments/{appointment}', [AppointmentController::class, 'update']);
        
        // Availability checking routes
        Route::post('/api/appointments/check-availability', [AppointmentController::class, 'checkAvailability']);
        Route::get('/api/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots']);
    });
    
    Route::middleware(['role:admin,doctor,nurse,receptionist', 'subscription.limits:add_appointment'])->group(function () {
        Route::post('/api/appointments', [AppointmentController::class, 'store']);
    });
    
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::delete('/api/appointments/{appointment}', [AppointmentController::class, 'destroy']);
    });
    
    // Surgeries API - Doctors and admin only
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::get('/api/surgeries', [SurgeryController::class, 'index']);
        Route::post('/api/surgeries', [SurgeryController::class, 'store']);
        Route::get('/api/surgeries/{surgery}', [SurgeryController::class, 'show']);
        Route::put('/api/surgeries/{surgery}', [SurgeryController::class, 'update']);
        Route::delete('/api/surgeries/{surgery}', [SurgeryController::class, 'destroy']);
    });
    
    // Medications API - Medical staff with subscription check
    Route::middleware(['role:admin,doctor,nurse', 'subscription.feature:inventory_management'])->group(function () {
        Route::get('/api/medications', [MedicationController::class, 'index']);
        Route::post('/api/medications', [MedicationController::class, 'store']);
        Route::get('/api/medications/{medication}', [MedicationController::class, 'show']);
        Route::put('/api/medications/{medication}', [MedicationController::class, 'update']);
        Route::delete('/api/medications/{medication}', [MedicationController::class, 'destroy']);
    });
    
    // Invoices API - Admin and accountant
    Route::middleware(['role:admin,accountant'])->group(function () {
        Route::get('/api/invoices', [InvoiceController::class, 'index']);
        Route::post('/api/invoices', [InvoiceController::class, 'store']);
        Route::get('/api/invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::put('/api/invoices/{invoice}', [InvoiceController::class, 'update']);
        Route::delete('/api/invoices/{invoice}', [InvoiceController::class, 'destroy']);
    });
    
    // Clinics API - Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/api/clinics', [ClinicController::class, 'index']);
        Route::get('/api/clinics/{clinic}', [ClinicController::class, 'show']);
        Route::put('/api/clinics/{clinic}', [ClinicController::class, 'update']);
        Route::delete('/api/clinics/{clinic}', [ClinicController::class, 'destroy']);
    });
    
    Route::middleware(['role:admin', 'subscription.limits:add_location'])->group(function () {
        Route::post('/api/clinics', [ClinicController::class, 'store']);
    });
    
    // Exams API - Medical and lab staff
    Route::middleware(['role:admin,doctor,lab_technician'])->group(function () {
        Route::get('/api/exams', [MedicalExamController::class, 'index']);
        Route::post('/api/exams', [MedicalExamController::class, 'store']);
        Route::get('/api/exams/{exam}', [MedicalExamController::class, 'show']);
        Route::put('/api/exams/{exam}', [MedicalExamController::class, 'update']);
        Route::delete('/api/exams/{exam}', [MedicalExamController::class, 'destroy']);
        Route::patch('/api/exams/{exam}/status', [MedicalExamController::class, 'updateStatus']);
    });
    
    // User Management API routes - Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/api/users/stats', [UserController::class, 'stats']);
        Route::get('/api/users/roles', [UserController::class, 'roles']);
        Route::get('/api/users', [UserController::class, 'index']);
        Route::post('/api/users', [UserController::class, 'store']);
        Route::get('/api/users/{user}', [UserController::class, 'show']);
        Route::put('/api/users/{user}', [UserController::class, 'update']);
        Route::delete('/api/users/{user}', [UserController::class, 'destroy']);
        Route::patch('/api/users/{user}/status', [UserController::class, 'updateStatus']);
        Route::patch('/api/users/{user}/password', [UserController::class, 'resetPassword']);
    });
    
    // Basic users list for filters (available to medical staff)
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/users/basic', [UserController::class, 'basicList']);
    });
    
    // Treatments API - Medical staff
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/treatments', [TreatmentController::class, 'index']);
        Route::get('/api/treatments/{treatment}', [TreatmentController::class, 'show']);
        Route::get('/api/treatments/stats', [TreatmentController::class, 'statistics']);
    });
    
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::post('/api/treatments', [TreatmentController::class, 'store']);
        Route::put('/api/treatments/{treatment}', [TreatmentController::class, 'update']);
        Route::delete('/api/treatments/{treatment}', [TreatmentController::class, 'destroy']);
        
        // Treatment sharing
        Route::post('/api/treatments/{treatment}/share-email', [TreatmentController::class, 'shareViaEmail']);
        Route::get('/api/treatments/{treatment}/qr', [TreatmentController::class, 'generateQR']);
        Route::get('/api/treatments/{treatment}/whatsapp', [TreatmentController::class, 'getWhatsAppUrl']);
        Route::get('/api/treatments/{treatment}/pdf', [TreatmentController::class, 'downloadPDF']);
    });
    
    // Doctor-Patient Relationships API - Admin and doctors
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::get('/api/relationships', [DoctorPatientRelationshipController::class, 'index']);
        Route::post('/api/relationships', [DoctorPatientRelationshipController::class, 'store']);
        Route::get('/api/relationships/{relationship}', [DoctorPatientRelationshipController::class, 'show']);
        Route::put('/api/relationships/{relationship}', [DoctorPatientRelationshipController::class, 'update']);
        Route::delete('/api/relationships/{relationship}', [DoctorPatientRelationshipController::class, 'destroy']);
        Route::get('/api/relationships/stats', [DoctorPatientRelationshipController::class, 'statistics']);
        
        // Specific doctor/patient relationships
        Route::get('/api/doctors/{doctor}/patients', [DoctorPatientRelationshipController::class, 'getDoctorPatients']);
        Route::get('/api/patients/{patient}/doctors', [DoctorPatientRelationshipController::class, 'getPatientDoctors']);
        
        // Patient transfer
        Route::post('/api/relationships/transfer', [DoctorPatientRelationshipController::class, 'transferPatient']);
    });
    
    // Enable booking - Available to all authenticated users (for their own profile)
    Route::post('/api/users/{user}/enable-booking', [UserController::class, 'enableBooking']);
    
    // Payment Methods API - Doctors and admin
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::get('/api/payment-methods', [PaymentMethodController::class, 'index']);
        Route::post('/api/payment-methods', [PaymentMethodController::class, 'store']);
        Route::get('/api/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'show']);
        Route::put('/api/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update']);
        Route::delete('/api/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy']);
        Route::post('/api/payment-methods/update-order', [PaymentMethodController::class, 'updateOrder']);
    });
    
    // Public endpoint for getting doctor's payment methods
    Route::get('/api/doctors/{doctor}/payment-methods', [PaymentMethodController::class, 'getForDoctor']);
    
    // Appointment Payments API - Admin and doctors
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/appointment-payments', [AppointmentPaymentController::class, 'index']);
        Route::get('/api/appointment-payments/{payment}', [AppointmentPaymentController::class, 'show']);
        Route::post('/api/appointment-payments/{payment}/verify', [AppointmentPaymentController::class, 'verify']);
        Route::post('/api/appointment-payments/{payment}/reject', [AppointmentPaymentController::class, 'reject']);
        Route::get('/api/appointment-payments/stats', [AppointmentPaymentController::class, 'stats']);
    });
    
    // Payment link generation for appointments
    Route::middleware(['role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::post('/api/appointments/{appointment}/generate-payment-link', [PaymentController::class, 'generateLink']);
    });
});

// Public treatment view (via QR code)
Route::get('/treatment/{qr}', [TreatmentController::class, 'publicView'])->name('treatments.public');

// Rutas públicas para reservas (sin autenticación)
Route::prefix('booking')->name('booking.')->group(function () {
    // Página principal de reservas por médico/clínica (con CSRF)
    Route::get('/{slug}', function ($slug) {
        return view('public.booking.index', compact('slug'));
    })->name('index');
    
    // Confirmación de reserva (con CSRF)
    Route::get('/{slug}/confirmation/{token}', [BookingController::class, 'confirmation'])->name('confirmation');
});

// Public payment routes (no authentication required)
Route::prefix('payments')->name('payments.')->group(function () {
    // Payment information (via QR/link)
    Route::get('/show/{reference}', [PaymentController::class, 'show'])->name('show');
    
    // Payment options for appointment
    Route::get('/options/{appointment}', [PaymentController::class, 'paymentOptions'])->name('options');
    Route::get('/token/{token}', [PaymentController::class, 'showPaymentOptionsWithToken'])->name('options-token');
    
    // Create payment
    Route::post('/create/{appointment}', [PaymentController::class, 'createPayment'])->name('create');
    
    // Manual payment forms
    Route::get('/manual/{reference}', [PaymentController::class, 'showManualPaymentForm'])->name('manual');
    Route::post('/manual/{reference}', [PaymentController::class, 'processManualPayment'])->name('manual.process');
    
    // Payment callbacks
    Route::get('/success/{payment}', [PaymentController::class, 'success'])->name('success');
    Route::get('/cancel/{appointment}', [PaymentController::class, 'cancel'])->name('cancel');
    
    // QR code generation
    Route::get('/qr/{reference}', [PaymentController::class, 'generateQR'])->name('qr');
    Route::get('/qr-link/{token}', [PaymentController::class, 'generateQR'])->name('qr-link');
    
    // Webhooks for payment providers
    Route::post('/webhook/paypal', [PaymentController::class, 'paypalWebhook'])->name('webhook.paypal');
    Route::post('/webhook/stripe', [PaymentController::class, 'stripeWebhook'])->name('webhook.stripe');
});

// Video Call Routes - Authenticated users only
Route::middleware('auth:web')->group(function () {
    // Video call management - Doctors and admins
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::post('/api/appointments/{appointment}/video-call', [App\Http\Controllers\VideoCallController::class, 'create'])->name('video-calls.create');
        Route::post('/api/video-calls/{videoCall}/start', [App\Http\Controllers\VideoCallController::class, 'start'])->name('video-calls.start');
        Route::post('/api/video-calls/{videoCall}/end', [App\Http\Controllers\VideoCallController::class, 'end'])->name('video-calls.end');
        Route::post('/api/video-calls/instant', [App\Http\Controllers\VideoCallController::class, 'createInstant'])->name('video-calls.instant');
    });
    
    // Video call participation - All authenticated users can join any video call (room access controlled by URL)
    Route::get('/video-calls/{videoCall}', [App\Http\Controllers\VideoCallController::class, 'show'])->name('video-calls.show');
    Route::post('/api/video-calls/{videoCall}/join', [App\Http\Controllers\VideoCallController::class, 'join'])->name('video-calls.join');
    Route::get('/api/video-calls/{videoCall}/status', [App\Http\Controllers\VideoCallController::class, 'status'])->name('video-calls.status');
});
