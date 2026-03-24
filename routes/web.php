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
use App\Http\Controllers\PaymentLinkController;

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
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/patients', function () {
            return view('pages.patients.index');
        })->name('patients.index');

        Route::middleware(['check.subscription.limits:add_patient'])->group(function () {
            Route::get('/patients/create', function () {
                return view('pages.patients.create');
            })->name('patients.create');
        });

        Route::get('/patients/{id}', function ($id) {
            return view('pages.patients.show', compact('id'));
        })->name('patients.show');

        Route::get('/patients/{id}/edit', function ($id) {
            return view('pages.patients.edit', compact('id'));
        })->name('patients.edit');
    });

    // Rutas de Doctores - Solo admin puede gestionar doctores
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/doctors', function () {
            return view('pages.doctors.index');
        })->name('doctors.index');

        Route::get('/doctors/{id}', function ($id) {
            return view('pages.doctors.show', compact('id'));
        })->name('doctors.show');
    });

    Route::middleware(['check.role:admin', 'check.subscription.limits:add_doctor'])->group(function () {
        Route::get('/doctors/create', function () {
            return view('pages.doctors.create');
        })->name('doctors.create');
    });

    // Rutas de Citas - Acceso por roles
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/appointments', function () {
            return view('pages.appointments.index');
        })->name('appointments.index');

    });

    Route::middleware(['check.role:admin,doctor,nurse,receptionist', 'check.subscription.limits:add_appointment'])->group(function () {
        Route::get('/appointments/create', function () {
            return view('pages.appointments.create', ['authUser' => auth()->user()]);
        })->name('appointments.create');
    });

    // Rutas de Cirugías - Solo doctores y admin
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        Route::get('/surgeries', function () {
            return view('pages.surgeries.index');
        })->name('surgeries.index');

        Route::get('/surgeries/create', function () {
            return view('pages.surgeries.create');
        })->name('surgeries.create');
    });

    // Rutas de Medicamentos - Acceso por roles y suscripción
    Route::middleware(['check.role:admin,doctor,nurse', 'check.subscription.feature:inventory_management'])->group(function () {
        Route::get('/medications', function () {
            return view('pages.medications.index');
        })->name('medications.index');

        Route::get('/medications/create', function () {
            return view('pages.medications.create');
        })->name('medications.create');
    });

    // Rutas de Facturas - Admin y contabilidad
    Route::middleware(['check.role:admin,accountant'])->group(function () {
        Route::get('/invoices', function () {
            return view('pages.invoices.index');
        })->name('invoices.index');

        Route::get('/invoices/create', function () {
            return view('pages.invoices.create');
        })->name('invoices.create');
    });

    // Rutas de Clínicas - Solo admin
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/clinics', function () {
            return view('pages.clinics.index');
        })->name('clinics.index');
    });

    Route::middleware(['check.role:admin', 'check.subscription.limits:add_location'])->group(function () {
        Route::get('/clinics/create', function () {
            return view('pages.clinics.create');
        })->name('clinics.create');
    });

    // Rutas de Exámenes - Acceso por roles
    Route::middleware(['check.role:admin,doctor,lab_technician'])->group(function () {
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
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        Route::get('/payment-methods', function () {
            return view('pages.payment-methods.index');
        })->name('payment-methods.web.index');

        Route::get('/payment-methods/create', function () {
            return view('pages.payment-methods.create');
        })->name('payment-methods.web.create');

        Route::get('/payment-methods/{id}', function ($id) {
            return view('pages.payment-methods.show', compact('id'));
        })->name('payment-methods.web.show');

        Route::get('/payment-methods/{id}/edit', function ($id) {
            return view('pages.payment-methods.edit', compact('id'));
        })->name('payment-methods.web.edit');

        Route::get('/payment-links', [PaymentLinkController::class, 'index'])->name('payment-links.index');
    });

    // Rutas de Administración de Usuarios - Solo admin
    Route::middleware(['check.role:admin'])->group(function () {
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

// ─────────────────────────────────────────────────────────────────────────────
// AUTHENTICATED API ROUTES (web session)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth:web')->group(function () {

    // Current user info
    Route::get('/api/user', function () {
        return response()->json(Auth::user());
    })->name('api.user');

    Route::get('/api/user/consultation-fee', function () {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'consultation_fee' => $user->consultation_fee ?? 0,
            'currency' => $user->currency ?? 'USD',
        ]);
    })->name('api.user.consultation-fee');

    // Dashboard
    Route::get('/api/dashboard/stats',           [DashboardController::class, 'stats']);
    Route::get('/api/dashboard/recent-activity', [DashboardController::class, 'recentActivity']);

    // ── Stats (all BEFORE wildcard routes) ───────────────────────────────────
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/appointments/stats',           [AppointmentController::class, 'stats']);
        Route::get('/api/dashboard/patients-statistics',[PatientController::class, 'stats']);
    });
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        Route::get('/api/surgeries/stats', [SurgeryController::class, 'stats']);
    });
    Route::middleware(['check.role:admin,doctor,lab_technician'])->group(function () {
        Route::get('/api/exams/stats', [MedicalExamController::class, 'stats']);
    });
    Route::middleware(['check.role:admin,doctor,nurse', 'check.subscription.feature:inventory_management'])->group(function () {
        Route::get('/api/medications/stats', [MedicationController::class, 'stats']);
    });
    Route::middleware(['check.role:admin,accountant'])->group(function () {
        Route::get('/api/invoices/stats', [InvoiceController::class, 'stats']);
    });
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/api/clinics/stats', [ClinicController::class, 'stats']);
        Route::get('/api/doctors/stats', [DoctorController::class, 'stats']);
        Route::get('/api/users/stats',   [UserController::class, 'stats']);
        Route::get('/api/users/roles',   [UserController::class, 'roles']);
    });
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/appointment-payments/stats', [AppointmentPaymentController::class, 'stats']);
    });
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/treatments/stats', [TreatmentController::class, 'statistics']);
    });
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        Route::get('/api/relationships/stats',   [DoctorPatientRelationshipController::class, 'statistics']);
        Route::post('/api/relationships/transfer',[DoctorPatientRelationshipController::class, 'transferPatient']);
    });

    // ── Patients ──────────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/patients',                          [PatientController::class, 'index']);
        Route::get('/api/patients/{patient}',                [PatientController::class, 'show']);
        Route::put('/api/patients/{patient}',                [PatientController::class, 'update']);
        Route::get('/api/patients/{patient}/appointments',   [PatientController::class, 'appointments']);
        Route::get('/api/patients/{patient}/doctors',        [DoctorPatientRelationshipController::class, 'getPatientDoctors']);
    });
    Route::middleware(['check.role:admin,doctor,nurse,receptionist', 'check.subscription.limits:add_patient'])->group(function () {
        Route::post('/api/patients', [PatientController::class, 'store']);
    });
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        Route::delete('/api/patients/{patient}', [PatientController::class, 'destroy']);
    });

    // ── Doctors ───────────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/doctors/list', [DoctorController::class, 'basicList']);
    });
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/api/doctors',          [DoctorController::class, 'index']);
        Route::get('/api/doctors/{doctor}', [DoctorController::class, 'show']);
        Route::put('/api/doctors/{doctor}', [DoctorController::class, 'update']);
        Route::delete('/api/doctors/{doctor}', [DoctorController::class, 'destroy']);
    });
    Route::middleware(['check.role:admin', 'check.subscription.limits:add_doctor'])->group(function () {
        Route::post('/api/doctors', [DoctorController::class, 'store']);
    });

    // ── Appointments ──────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        // Static routes BEFORE /{appointment} wildcard
        Route::get('/api/appointments/available-slots',    [AppointmentController::class, 'getAvailableSlots']);
        Route::get('/api/appointments/booked-slots',       [AppointmentController::class, 'bookedSlots']);
        Route::post('/api/appointments/check-availability',[AppointmentController::class, 'checkAvailability']);

        Route::get('/api/appointments',              [AppointmentController::class, 'index']);
        Route::get('/api/appointments/{appointment}',[AppointmentController::class, 'show']);
        Route::put('/api/appointments/{appointment}',[AppointmentController::class, 'update']);
    });
    Route::middleware(['check.role:admin,doctor,nurse,receptionist', 'check.subscription.limits:add_appointment'])->group(function () {
        Route::post('/api/appointments', [AppointmentController::class, 'store']);
    });
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        Route::delete('/api/appointments/{appointment}', [AppointmentController::class, 'destroy']);
    });

    // ── Surgeries ─────────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        Route::get('/api/surgeries',             [SurgeryController::class, 'index']);
        Route::post('/api/surgeries',            [SurgeryController::class, 'store']);
        Route::get('/api/surgeries/{surgery}',   [SurgeryController::class, 'show']);
        Route::put('/api/surgeries/{surgery}',   [SurgeryController::class, 'update']);
        Route::delete('/api/surgeries/{surgery}',[SurgeryController::class, 'destroy']);
    });

    // ── Medications ───────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor,nurse', 'check.subscription.feature:inventory_management'])->group(function () {
        Route::get('/api/medications',              [MedicationController::class, 'index']);
        Route::post('/api/medications',             [MedicationController::class, 'store']);
        Route::get('/api/medications/{medication}', [MedicationController::class, 'show']);
        Route::put('/api/medications/{medication}', [MedicationController::class, 'update']);
        Route::delete('/api/medications/{medication}', [MedicationController::class, 'destroy']);
    });

    // ── Invoices ──────────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,accountant'])->group(function () {
        Route::get('/api/invoices',           [InvoiceController::class, 'index']);
        Route::post('/api/invoices',          [InvoiceController::class, 'store']);
        Route::get('/api/invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::put('/api/invoices/{invoice}', [InvoiceController::class, 'update']);
        Route::delete('/api/invoices/{invoice}', [InvoiceController::class, 'destroy']);
    });

    // ── Clinics ───────────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/api/clinics',          [ClinicController::class, 'index']);
        Route::get('/api/clinics/{clinic}', [ClinicController::class, 'show']);
        Route::put('/api/clinics/{clinic}', [ClinicController::class, 'update']);
        Route::delete('/api/clinics/{clinic}', [ClinicController::class, 'destroy']);
    });
    Route::middleware(['check.role:admin', 'check.subscription.limits:add_location'])->group(function () {
        Route::post('/api/clinics', [ClinicController::class, 'store']);
    });

    // ── Exams ─────────────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor,lab_technician'])->group(function () {
        Route::get('/api/exams',          [MedicalExamController::class, 'index']);
        Route::post('/api/exams',         [MedicalExamController::class, 'store']);
        Route::get('/api/exams/{exam}',   [MedicalExamController::class, 'show']);
        Route::put('/api/exams/{exam}',   [MedicalExamController::class, 'update']);
        Route::delete('/api/exams/{exam}',[MedicalExamController::class, 'destroy']);
        Route::patch('/api/exams/{exam}/status', [MedicalExamController::class, 'updateStatus']);
    });

    // ── Users ─────────────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor,receptionist'])->group(function () {
        Route::get('/api/users', [UserController::class, 'index']);
    });
    Route::middleware(['check.role:admin'])->group(function () {
        Route::post('/api/users',                    [UserController::class, 'store']);
        Route::get('/api/users/{user}',              [UserController::class, 'show']);
        Route::put('/api/users/{user}',              [UserController::class, 'update']);
        Route::delete('/api/users/{user}',           [UserController::class, 'destroy']);
        Route::patch('/api/users/{user}/status',     [UserController::class, 'updateStatus']);
        Route::patch('/api/users/{user}/password',   [UserController::class, 'resetPassword']);
    });
    Route::post('/api/users/{user}/enable-booking', [UserController::class, 'enableBooking']);

    // ── Treatments ────────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/treatments',             [TreatmentController::class, 'index']);
        Route::get('/api/treatments/{treatment}', [TreatmentController::class, 'show']);
    });
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        Route::post('/api/treatments',                              [TreatmentController::class, 'store']);
        Route::put('/api/treatments/{treatment}',                   [TreatmentController::class, 'update']);
        Route::delete('/api/treatments/{treatment}',                [TreatmentController::class, 'destroy']);
        Route::post('/api/treatments/{treatment}/share-email',      [TreatmentController::class, 'shareViaEmail']);
        Route::get('/api/treatments/{treatment}/qr',                [TreatmentController::class, 'generateQR']);
        Route::get('/api/treatments/{treatment}/whatsapp',          [TreatmentController::class, 'getWhatsAppUrl']);
        Route::get('/api/treatments/{treatment}/pdf',               [TreatmentController::class, 'downloadPDF']);
    });

    // ── Doctor-Patient Relationships ──────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        Route::get('/api/relationships',                [DoctorPatientRelationshipController::class, 'index']);
        Route::post('/api/relationships',               [DoctorPatientRelationshipController::class, 'store']);
        Route::get('/api/relationships/{relationship}', [DoctorPatientRelationshipController::class, 'show']);
        Route::put('/api/relationships/{relationship}', [DoctorPatientRelationshipController::class, 'update']);
        Route::delete('/api/relationships/{relationship}', [DoctorPatientRelationshipController::class, 'destroy']);
        Route::get('/api/doctors/{doctor}/patients',    [DoctorPatientRelationshipController::class, 'getDoctorPatients']);
    });

    // ── Payment Methods ───────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        // Static routes BEFORE /{paymentMethod} wildcard
        Route::post('/api/payment-methods/generate-link', [PaymentMethodController::class, 'generatePaymentLink'])
             ->middleware('check.payment.setup');
        Route::get('/api/payment-methods/payment-data',   [PaymentMethodController::class, 'getPaymentLinkData'])
             ->middleware('check.payment.setup');

        Route::get('/api/payment-methods',                  [PaymentMethodController::class, 'index']);
        Route::post('/api/payment-methods',                 [PaymentMethodController::class, 'store']);
        Route::get('/api/payment-methods/{paymentMethod}',  [PaymentMethodController::class, 'show']);
        Route::put('/api/payment-methods/{paymentMethod}',  [PaymentMethodController::class, 'update']);
        Route::delete('/api/payment-methods/{paymentMethod}',[PaymentMethodController::class, 'destroy']);
    });

    // ── Payment Links ─────────────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor'])->group(function () {
        // Static routes BEFORE /{id} wildcard
        Route::get('/api/payment-links/create-data', [PaymentLinkController::class, 'getCreateData']);
        Route::get('/api/payment-links/stats',        [PaymentLinkController::class, 'getStats']);

        Route::get('/api/payment-links',              [PaymentLinkController::class, 'getLinks']);
        Route::post('/api/payment-links',             [PaymentLinkController::class, 'store']);
        Route::get('/api/payment-links/{id}',         [PaymentLinkController::class, 'show']);
        Route::patch('/api/payment-links/{id}/deactivate', [PaymentLinkController::class, 'deactivate']);
        Route::delete('/api/payment-links/{id}',      [PaymentLinkController::class, 'destroy']);
        Route::get('/api/payment-links/{id}/qr',      [PaymentLinkController::class, 'generateQr']);
    });

    // ── Appointment Payments ──────────────────────────────────────────────────
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::get('/api/appointment-payments',                  [AppointmentPaymentController::class, 'index']);
        Route::get('/api/appointment-payments/{payment}',        [AppointmentPaymentController::class, 'show']);
        Route::post('/api/appointment-payments/{payment}/verify',[AppointmentPaymentController::class, 'verify']);
        Route::post('/api/appointment-payments/{payment}/reject',[AppointmentPaymentController::class, 'reject']);
    });
    Route::middleware(['check.role:admin,doctor,nurse,receptionist'])->group(function () {
        Route::post('/api/appointments/{appointment}/generate-payment-link', [PaymentController::class, 'generateLink']);
    });
});

// Public treatment view (via QR code)
Route::get('/treatment/{qr}', [TreatmentController::class, 'publicView'])->name('treatments.public');

// Public Payment Links (no authentication required)
Route::get('/pay/{token}',     [PaymentLinkController::class, 'showPublic'])->name('payment-link.show');
Route::get('/pay/{token}/qr',  [PaymentLinkController::class, 'generateQr'])->name('payment-link.qr');
Route::get('/api/payment-links/{token}/info',            [PaymentLinkController::class, 'getPublicInfo']);
Route::post('/api/payment-links/{token}/process',        [PaymentLinkController::class, 'processPayment']);
Route::post('/api/payment-links/{token}/confirm-manual', [PaymentLinkController::class, 'confirmManualPayment']);
Route::get('/api/doctors/{doctor}/payment-methods',      [PaymentMethodController::class, 'getForDoctor']);

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

// Public Video Call Routes (no authentication required for guests)
Route::prefix('room')->name('video-calls.public.')->group(function () {
    // Public access to video call rooms - Anyone with the link can join
    Route::get('/{videoCall}', [App\Http\Controllers\VideoCallController::class, 'showPublic'])->name('show');
    Route::post('/api/{videoCall}/join-guest', [App\Http\Controllers\VideoCallController::class, 'joinAsGuest'])->name('join-guest');
});

// Video Call Routes - Authenticated users only
Route::middleware('auth:web')->group(function () {
    // Video call management - Doctors and admins
    Route::middleware(['check.role:admin,doctor'])->group(function () {
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
