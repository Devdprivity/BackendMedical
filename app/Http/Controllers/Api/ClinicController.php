<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Clinic::query();
        
        // Non-admin users see limited clinic data
        $user = auth()->user();
        if ($user->role !== 'admin') {
            // Non-admin users might see only their assigned clinic(s)
            $doctor = $user->doctor;
            if ($doctor && $doctor->clinics()->exists()) {
                // Show only clinics where this doctor works
                $clinicIds = $doctor->clinics()->pluck('clinics.id');
                $query->whereIn('id', $clinicIds);
            } else {
                // If no clinic assignment, show no clinics
                $query->where('id', -1);
            }
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $clinics = $query->withCount(['doctors', 'patients', 'appointments'])
            ->paginate($request->get('per_page', 15));

        // Add monthly statistics (filtered by user permissions)
        $clinics->getCollection()->transform(function ($clinic) use ($user) {
            $clinic->monthly_appointments = $clinic->appointments()
                ->whereMonth('date_time', now()->month)
                ->count();
            
            // Only show financial data to admin
            if ($user->role === 'admin') {
                $clinic->monthly_income = $clinic->appointments()
                    ->whereMonth('date_time', now()->month)
                    ->join('invoices', 'appointments.patient_id', '=', 'invoices.patient_id')
                    ->where('invoices.payment_status', 'paid')
                    ->sum('invoices.total');
            } else {
                $clinic->monthly_income = 0; // Hidden for non-admin
            }
                
            return $clinic;
        });

        return response()->json($clinics);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'medical_director' => 'required|string',
            'foundation_year' => 'required|integer|min:1900|max:' . date('Y'),
            'specialties' => 'required|array',
            'schedule' => 'required|array',
            'emergency_services' => 'boolean',
            'status' => 'in:active,inactive,maintenance',
            'description' => 'nullable|string',
        ]);

        $clinic = Clinic::create($request->all());

        return response()->json($clinic, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Clinic $clinic)
    {
        $clinic->load(['doctors', 'patients']);
        
        // Add detailed statistics
        $clinic->statistics = [
            'total_appointments' => $clinic->appointments()->count(),
            'monthly_income' => $clinic->appointments()
                ->whereMonth('date_time', now()->month)
                ->join('invoices', 'appointments.patient_id', '=', 'invoices.patient_id')
                ->where('invoices.payment_status', 'paid')
                ->sum('invoices.total'),
            'occupancy_rate' => $this->calculateOccupancyRate($clinic),
            'patient_satisfaction' => $this->calculatePatientSatisfaction($clinic),
        ];

        return response()->json($clinic);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Clinic $clinic)
    {
        $request->validate([
            'name' => 'string|max:255',
            'address' => 'string',
            'phone' => 'string',
            'email' => 'email',
            'medical_director' => 'string',
            'foundation_year' => 'integer|min:1900|max:' . date('Y'),
            'specialties' => 'array',
            'schedule' => 'array',
            'emergency_services' => 'boolean',
            'status' => 'in:active,inactive,maintenance',
            'description' => 'nullable|string',
        ]);

        $clinic->update($request->all());

        return response()->json($clinic);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clinic $clinic)
    {
        $clinic->delete();

        return response()->json(['message' => 'Clinic deleted successfully']);
    }

    /**
     * Get doctors for a specific clinic.
     */
    public function doctors(Clinic $clinic)
    {
        $doctors = $clinic->doctors()
            ->withPivot('status', 'schedule')
            ->with('user')
            ->get();

        return response()->json($doctors);
    }

    /**
     * Get patients for a specific clinic.
     */
    public function patients(Clinic $clinic)
    {
        $patients = $clinic->patients()
            ->with(['emergencyContact', 'medicalHistory'])
            ->paginate(15);

        return response()->json($patients);
    }

    /**
     * Get appointments for a specific clinic.
     */
    public function appointments(Clinic $clinic, Request $request)
    {
        $query = $clinic->appointments()
            ->with(['patient', 'doctor']);

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('date_time', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('date_time', '<=', $request->to_date);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('date_time', 'desc')
            ->paginate(15);

        return response()->json($appointments);
    }

    /**
     * Get clinic statistics.
     */
    public function stats()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin sees all clinic stats
            $stats = [
                'total_clinics' => Clinic::count(),
                'active_clinics' => Clinic::where('status', 'active')->count(),
                'inactive_clinics' => Clinic::where('status', 'inactive')->count(),
                'maintenance_clinics' => Clinic::where('status', 'maintenance')->count(),
                'total_beds' => Clinic::sum('total_beds'),
                'occupied_beds' => Clinic::sum('occupied_beds'),
                'emergency_services' => Clinic::where('emergency_services', true)->count(),
                'monthly_appointments' => Clinic::withCount(['appointments' => function($query) {
                    $query->whereMonth('date_time', now()->month);
                }])->get()->sum('appointments_count'),
            ];
        } else {
            // Non-admin users see stats only for their assigned clinics
            $doctor = $user->doctor;
            
            if (!$doctor || !$doctor->clinics()->exists()) {
                $stats = [
                    'total_clinics' => 0,
                    'active_clinics' => 0,
                    'inactive_clinics' => 0,
                    'maintenance_clinics' => 0,
                    'total_beds' => 0,
                    'occupied_beds' => 0,
                    'emergency_services' => 0,
                    'monthly_appointments' => 0,
                ];
            } else {
                $clinicIds = $doctor->clinics()->pluck('clinics.id');
                
                $stats = [
                    'total_clinics' => Clinic::whereIn('id', $clinicIds)->count(),
                    'active_clinics' => Clinic::whereIn('id', $clinicIds)->where('status', 'active')->count(),
                    'inactive_clinics' => Clinic::whereIn('id', $clinicIds)->where('status', 'inactive')->count(),
                    'maintenance_clinics' => Clinic::whereIn('id', $clinicIds)->where('status', 'maintenance')->count(),
                    'total_beds' => Clinic::whereIn('id', $clinicIds)->sum('total_beds'),
                    'occupied_beds' => Clinic::whereIn('id', $clinicIds)->sum('occupied_beds'),
                    'emergency_services' => Clinic::whereIn('id', $clinicIds)->where('emergency_services', true)->count(),
                    'monthly_appointments' => 0, // Calculated separately for security
                ];
            }
        }
        
        return response()->json($stats);
    }

    private function calculateOccupancyRate(Clinic $clinic)
    {
        // Simple calculation - can be improved based on actual business logic
        $totalSlots = $clinic->doctors()->count() * 8 * 30; // 8 hours, 30 days
        $bookedSlots = $clinic->appointments()
            ->whereMonth('date_time', now()->month)
            ->count();

        return $totalSlots > 0 ? round(($bookedSlots / $totalSlots) * 100, 2) : 0;
    }

    private function calculatePatientSatisfaction(Clinic $clinic)
    {
        // Placeholder - in real scenario, this would come from patient feedback
        return 4.5;
    }
}
