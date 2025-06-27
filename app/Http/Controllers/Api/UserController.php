<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();
        $currentUser = auth()->user();

        // Admin can see everyone
        if ($currentUser->role !== 'admin') {
            // Receptionists can see doctors
            if ($currentUser->role === 'receptionist' && $request->role === 'doctor') {
                // Allow receptionist to fetch doctors
            }
            // Doctors can only see themselves
            else if ($currentUser->role === 'doctor') {
                $query->where('id', $currentUser->id);
            }
            // Block other roles from seeing user lists
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para acceder a esta lista de usuarios.'
                ], 403);
            }
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to active users only
            $query->where('status', 'active');
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,doctor,nurse,receptionist,accountant,lab_technician',
            'status' => 'sometimes|in:active,inactive,suspended',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->get('status', 'active'),
            'email_verified_at' => now(),
        ]);

        return response()->json($user, 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:admin,doctor,nurse,receptionist,accountant,lab_technician',
            'status' => 'sometimes|in:active,inactive,suspended',
        ]);

        $updateData = $request->except(['password']);
        
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json($user);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deleting the last admin user
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->where('status', 'active')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'message' => 'Cannot delete the last active admin user'
                ], 400);
            }
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Update user status.
     */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Prevent deactivating the last admin user
        if ($user->role === 'admin' && $request->status !== 'active') {
            $activeAdminCount = User::where('role', 'admin')
                ->where('status', 'active')
                ->where('id', '!=', $user->id)
                ->count();
            
            if ($activeAdminCount < 1) {
                return response()->json([
                    'message' => 'Cannot deactivate the last active admin user'
                ], 400);
            }
        }

        $user->update(['status' => $request->status]);

        return response()->json($user);
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password reset successfully']);
    }

    /**
     * Get user statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'by_role' => [
                'admin' => User::where('role', 'admin')->count(),
                'doctor' => User::where('role', 'doctor')->count(),
                'nurse' => User::where('role', 'nurse')->count(),
                'receptionist' => User::where('role', 'receptionist')->count(),
                'accountant' => User::where('role', 'accountant')->count(),
                'lab_technician' => User::where('role', 'lab_technician')->count(),
            ],
            'recent' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get available roles and their permissions.
     */
    public function roles(): JsonResponse
    {
        $roles = [
            'admin' => [
                'name' => 'Administrador',
                'description' => 'Acceso completo al sistema',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'patients.view', 'patients.create', 'patients.edit', 'patients.delete',
                    'doctors.view', 'doctors.create', 'doctors.edit', 'doctors.delete',
                    'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.delete',
                    'surgeries.view', 'surgeries.create', 'surgeries.edit', 'surgeries.delete',
                    'medications.view', 'medications.create', 'medications.edit', 'medications.delete',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
                    'exams.view', 'exams.create', 'exams.edit', 'exams.delete',
                    'clinics.view', 'clinics.create', 'clinics.edit', 'clinics.delete',
                    'reports.view', 'settings.manage'
                ]
            ],
            'doctor' => [
                'name' => 'Doctor',
                'description' => 'Acceso a pacientes, citas y procedimientos médicos',
                'permissions' => [
                    'patients.view', 'patients.create', 'patients.edit',
                    'appointments.view', 'appointments.create', 'appointments.edit',
                    'surgeries.view', 'surgeries.create', 'surgeries.edit',
                    'exams.view', 'exams.create', 'exams.edit',
                    'medications.view',
                    'reports.view'
                ]
            ],
            'nurse' => [
                'name' => 'Enfermera',
                'description' => 'Acceso a pacientes y apoyo médico',
                'permissions' => [
                    'patients.view', 'patients.edit',
                    'appointments.view', 'appointments.edit',
                    'surgeries.view',
                    'exams.view', 'exams.edit',
                    'medications.view'
                ]
            ],
            'receptionist' => [
                'name' => 'Recepcionista',
                'description' => 'Gestión de citas y atención al cliente',
                'permissions' => [
                    'patients.view', 'patients.create', 'patients.edit',
                    'appointments.view', 'appointments.create', 'appointments.edit',
                    'invoices.view', 'invoices.create'
                ]
            ],
            'accountant' => [
                'name' => 'Contador',
                'description' => 'Gestión financiera y facturación',
                'permissions' => [
                    'patients.view',
                    'invoices.view', 'invoices.create', 'invoices.edit',
                    'medications.view',
                    'reports.view'
                ]
            ],
            'lab_technician' => [
                'name' => 'Técnico de Laboratorio',
                'description' => 'Gestión de exámenes y resultados',
                'permissions' => [
                    'patients.view',
                    'exams.view', 'exams.edit',
                    'appointments.view'
                ]
            ]
        ];

        return response()->json($roles);
    }

    /**
     * Enable booking for a user
     */
    public function enableBooking(User $user): JsonResponse
    {
        // Only allow users to enable booking for themselves or admins to enable for others
        if (auth()->id() !== $user->id && auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'No tienes permisos para modificar este usuario'
            ], 403);
        }

        // Don't allow booking for admin users
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Los administradores no pueden tener reservas públicas'
            ], 400);
        }

        // Generate booking slug if not exists
        $bookingSlug = $user->booking_slug;
        if (!$bookingSlug) {
            $bookingSlug = $this->generateBookingSlug($user->name);
        }

        // Update user with booking enabled
        $user->update([
            'booking_enabled' => true,
            'booking_slug' => $bookingSlug,
            'consultation_fee' => $user->consultation_fee ?? 0,
            'schedule_start' => $user->schedule_start ?? '08:00',
            'schedule_end' => $user->schedule_end ?? '17:00',
            'work_days' => $user->work_days ?? json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
        ]);

        return response()->json([
            'message' => 'Reservas públicas habilitadas exitosamente',
            'booking_url' => url('/booking/' . $bookingSlug),
            'user' => $user->fresh()
        ]);
    }

    /**
     * Generate a unique booking slug for a user
     */
    private function generateBookingSlug(string $name): string
    {
        // Convert name to slug format
        $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $baseSlug = 'dr-' . $baseSlug;
        
        // Ensure uniqueness
        $slug = $baseSlug;
        $counter = 1;
        
        while (User::where('booking_slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Get basic list of users for filters and dropdowns
     */
    public function basicList(Request $request): JsonResponse
    {
        $query = User::select('id', 'name', 'role', 'status')
                    ->where('status', 'active');

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')
                      ->paginate($request->get('per_page', 100));

        return response()->json($users);
    }
} 