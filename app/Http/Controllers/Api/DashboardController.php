<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Surgery;
use App\Models\MedicalExam;
use App\Models\Invoice;
use App\Models\Medication;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Traits\FiltersUserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use FiltersUserData;

    /**
     * Get dashboard statistics
     */
    public function stats(): JsonResponse
    {
        $user = auth()->user();
        
        // If user is admin, show all data
        if ($user->role === 'admin') {
            $stats = [
                'patients' => [
                    'total' => Patient::count(),
                    'new_today' => Patient::whereDate('created_at', today())->count(),
                    'active' => Patient::where('status', 'active')->count(),
                    'with_appointments_today' => Patient::whereHas('appointments', function($q) {
                        $q->whereDate('date_time', today());
                    })->count()
                ],
                'doctors' => [
                    'total' => Doctor::count(),
                    'available' => Doctor::where('status', 'active')->count(),
                    'with_appointments_today' => Doctor::whereHas('appointments', function($q) {
                        $q->whereDate('date_time', today());
                    })->count(),
                    'on_duty' => Doctor::where('status', 'active')->count()
                ],
                'appointments' => [
                    'total_today' => Appointment::whereDate('date_time', today())->count(),
                    'completed' => Appointment::whereDate('date_time', today())->where('status', 'completed')->count(),
                    'pending' => Appointment::whereDate('date_time', today())->where('status', 'scheduled')->count(),
                    'cancelled' => Appointment::whereDate('date_time', today())->where('status', 'cancelled')->count()
                ],
                'surgeries' => [
                    'scheduled_today' => Surgery::whereDate('date_time', today())->where('status', 'scheduled')->count(),
                    'in_progress' => Surgery::where('status', 'in_progress')->count(),
                    'completed_today' => Surgery::whereDate('date_time', today())->where('status', 'completed')->count(),
                    'total_this_week' => Surgery::whereBetween('date_time', [now()->startOfWeek(), now()->endOfWeek()])->count()
                ],
                'invoices' => [
                    'total_pending' => Invoice::where('status', 'pending')->count(),
                    'overdue' => Invoice::where('status', 'overdue')->count(),
                    'paid_today' => Invoice::whereDate('created_at', today())->where('status', 'paid')->count(),
                    'revenue_today' => Invoice::whereDate('created_at', today())->where('status', 'paid')->sum('total')
                ],
                'medications' => [
                    'total_items' => Medication::count(),
                    'low_stock' => Medication::whereRaw('stock <= min_stock')->count(),
                    'expiring_soon' => Medication::whereDate('expiration_date', '<=', now()->addDays(30))->count(),
                    'out_of_stock' => Medication::where('stock', 0)->count()
                ],
                'clinics' => [
                    'total' => Clinic::count(),
                    'active' => Clinic::where('status', 'active')->count(),
                    'total_beds' => Clinic::sum('total_beds'),
                    'occupied_beds' => Clinic::sum('occupied_beds')
                ]
            ];
        } else {
            // For non-admin users, show only their own filtered data
            $stats = $this->getUserSpecificStats($user);
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
            'user_role' => $user->role,
            'is_admin' => $user->role === 'admin'
        ]);
    }

    /**
     * Get user-specific statistics based on role and access
     */
    private function getUserSpecificStats($user): array
    {
        $stats = [];
        
        // Get filtered queries for each entity type
        $patientsQuery = $this->applyUserFilters(Patient::query(), request(), 'patients');
        $appointmentsQuery = $this->applyUserFilters(Appointment::query(), request(), 'appointments');
        
        // Base stats that all roles can see (filtered to their scope)
        $stats['patients'] = [
            'total' => $patientsQuery->count(),
            'new_today' => (clone $patientsQuery)->whereDate('created_at', today())->count(),
            'active' => (clone $patientsQuery)->where('status', 'active')->count(),
            'with_appointments_today' => (clone $patientsQuery)->whereHas('appointments', function($q) use ($user) {
                $q->whereDate('date_time', today());
                if ($user->role === 'doctor' && $user->doctor) {
                    $q->where('doctor_id', $user->doctor->id);
                }
            })->count()
        ];

        $stats['appointments'] = [
            'total_today' => $appointmentsQuery->whereDate('date_time', today())->count(),
            'completed' => (clone $appointmentsQuery)->whereDate('date_time', today())->where('status', 'completed')->count(),
            'pending' => (clone $appointmentsQuery)->whereDate('date_time', today())->where('status', 'scheduled')->count(),
            'cancelled' => (clone $appointmentsQuery)->whereDate('date_time', today())->where('status', 'cancelled')->count()
        ];

        // Role-specific additional stats
        switch ($user->role) {
            case 'doctor':
                if ($user->doctor) {
                    $surgeriesQuery = $this->applyUserFilters(Surgery::query(), request(), 'surgeries');
                    $examsQuery = $this->applyUserFilters(MedicalExam::query(), request(), 'exams');
                    $medicationsQuery = $this->applyUserFilters(Medication::query(), request(), 'medications');
                    
                    $stats['surgeries'] = [
                        'scheduled_today' => $surgeriesQuery->whereDate('date_time', today())->where('status', 'scheduled')->count(),
                        'in_progress' => (clone $surgeriesQuery)->where('status', 'in_progress')->count(),
                        'completed_today' => (clone $surgeriesQuery)->whereDate('date_time', today())->where('status', 'completed')->count(),
                        'total_this_week' => (clone $surgeriesQuery)->whereBetween('date_time', [now()->startOfWeek(), now()->endOfWeek()])->count()
                    ];
                    
                    $stats['exams'] = [
                        'scheduled_today' => $examsQuery->whereDate('scheduled_date', today())->where('status', 'scheduled')->count(),
                        'pending' => (clone $examsQuery)->where('status', 'scheduled')->count(),
                        'completed_today' => (clone $examsQuery)->whereDate('scheduled_date', today())->where('status', 'completed')->count(),
                        'total_this_week' => (clone $examsQuery)->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()])->count()
                    ];
                    
                    $stats['medications'] = [
                        'available' => $medicationsQuery->count(),
                        'low_stock' => (clone $medicationsQuery)->whereColumn('current_stock', '<=', 'min_stock')->count(),
                        'out_of_stock' => 0, // Hidden for doctors
                        'prescription_required' => (clone $medicationsQuery)->where('requires_prescription', true)->count()
                    ];
                }
                break;
                
            case 'nurse':
                $medicationsQuery = $this->applyUserFilters(Medication::query(), request(), 'medications');
                
                $stats['medications'] = [
                    'available' => $medicationsQuery->count(),
                    'low_stock' => (clone $medicationsQuery)->whereColumn('current_stock', '<=', 'min_stock')->count(),
                    'controlled' => (clone $medicationsQuery)->where('controlled', true)->count(),
                    'prescription_required' => (clone $medicationsQuery)->where('requires_prescription', true)->count()
                ];
                break;
                
            case 'receptionist':
                // Receptionists only see patient and appointment stats
                break;
                
            case 'lab_technician':
                $examsQuery = $this->applyUserFilters(MedicalExam::query(), request(), 'exams');
                
                $stats['exams'] = [
                    'pending' => $examsQuery->where('status', 'scheduled')->count(),
                    'in_progress' => (clone $examsQuery)->where('status', 'in_progress')->count(),
                    'completed_today' => (clone $examsQuery)->whereDate('scheduled_date', today())->where('status', 'completed')->count(),
                    'total_this_week' => (clone $examsQuery)->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()])->count()
                ];
                break;
                
            case 'accountant':
                $invoicesQuery = $this->applyUserFilters(Invoice::query(), request(), 'invoices');
                
                $stats['invoices'] = [
                    'total_pending' => $invoicesQuery->where('status', 'pending')->count(),
                    'overdue' => (clone $invoicesQuery)->where('status', 'overdue')->count(),
                    'paid_today' => (clone $invoicesQuery)->whereDate('created_at', today())->where('status', 'paid')->count(),
                    'revenue_today' => (clone $invoicesQuery)->whereDate('created_at', today())->where('status', 'paid')->sum('total')
                ];
                break;
        }

        // Add role-specific context
        $stats['user_context'] = [
            'role' => $user->role,
            'name' => $user->name,
            'can_see_global_data' => false,
            'filtered_data' => true
        ];
        
        if ($user->role === 'doctor' && $user->doctor) {
            $stats['user_context']['doctor_name'] = $user->doctor->name;
            $stats['user_context']['specialty'] = $user->doctor->specialty;
        }

        return $stats;
    }

    /**
     * Get recent activity
     */
    public function recentActivity(): JsonResponse
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin sees global activity
            $activities = $this->getGlobalRecentActivity();
        } else {
            // Users see only their own activity
            $activities = $this->getUserRecentActivity($user);
        }

        return response()->json([
            'success' => true,
            'data' => $activities,
            'user_role' => $user->role
        ]);
    }

    /**
     * Get global recent activity (admin only)
     */
    private function getGlobalRecentActivity(): array
    {
        return [
            [
                'id' => 1,
                'type' => 'appointment',
                'title' => 'Nueva cita programada',
                'description' => 'Cita con Dr. García para Juan Pérez',
                'time' => '2 minutos',
                'icon' => 'calendar-check',
                'color' => 'primary'
            ],
            [
                'id' => 2,
                'type' => 'patient',
                'title' => 'Nuevo paciente registrado',
                'description' => 'María López se registró en el sistema',
                'time' => '15 minutos',
                'icon' => 'user-plus',
                'color' => 'success'
            ],
            [
                'id' => 3,
                'type' => 'surgery',
                'title' => 'Cirugía completada',
                'description' => 'Cirugía de apendicitis completada exitosamente',
                'time' => '1 hora',
                'icon' => 'procedures',
                'color' => 'info'
            ],
            [
                'id' => 4,
                'type' => 'medication',
                'title' => 'Stock bajo de medicamento',
                'description' => 'Paracetamol 500mg tiene stock bajo',
                'time' => '2 horas',
                'icon' => 'exclamation-triangle',
                'color' => 'warning'
            ],
            [
                'id' => 5,
                'type' => 'invoice',
                'title' => 'Factura pagada',
                'description' => 'Factura #INV-001 ha sido pagada',
                'time' => '3 horas',
                'icon' => 'credit-card',
                'color' => 'success'
            ]
        ];
    }

    /**
     * Get user-specific recent activity
     */
    private function getUserRecentActivity($user): array
    {
        $activities = [];
        
        switch ($user->role) {
            case 'doctor':
                if ($user->doctor) {
                    $activities = [
                        [
                            'id' => 1,
                            'type' => 'appointment',
                            'title' => 'Próxima cita',
                            'description' => 'Cita programada para las 10:00 AM',
                            'time' => '30 minutos',
                            'icon' => 'calendar-check',
                            'color' => 'primary'
                        ],
                        [
                            'id' => 2,
                            'type' => 'patient',
                            'title' => 'Paciente actualizado',
                            'description' => 'Historial médico actualizado',
                            'time' => '1 hora',
                            'icon' => 'user-edit',
                            'color' => 'info'
                        ]
                    ];
                }
                break;
                
            case 'nurse':
                $activities = [
                    [
                        'id' => 1,
                        'type' => 'medication',
                        'title' => 'Medicamento administrado',
                        'description' => 'Paracetamol administrado a paciente',
                        'time' => '15 minutos',
                        'icon' => 'pills',
                        'color' => 'success'
                    ]
                ];
                break;
                
            case 'receptionist':
                $activities = [
                    [
                        'id' => 1,
                        'type' => 'appointment',
                        'title' => 'Cita programada',
                        'description' => 'Nueva cita programada para mañana',
                        'time' => '10 minutos',
                        'icon' => 'calendar-plus',
                        'color' => 'primary'
                    ]
                ];
                break;
                
            default:
                $activities = [
                    [
                        'id' => 1,
                        'type' => 'system',
                        'title' => 'Sesión iniciada',
                        'description' => 'Acceso al sistema registrado',
                        'time' => 'Ahora',
                        'icon' => 'sign-in-alt',
                        'color' => 'info'
                    ]
                ];
        }
        
        return $activities;
    }

    /**
     * Debug user info and permissions
     */
    public function debug(): JsonResponse
    {
        $user = auth()->user();
        
        $debug = [
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
            'doctor_record' => $user->doctor ? [
                'id' => $user->doctor->id,
                'name' => $user->doctor->name,
                'specialty' => $user->doctor->specialty,
                'status' => $user->doctor->status,
            ] : null,
            'subscription_info' => [
                'has_subscription' => $user->subscription ? true : false,
                'plan' => $user->subscription->plan ?? 'free',
                'status' => $user->subscription->status ?? 'inactive',
                'limits' => $user->subscription ? [
                    'patients' => $user->subscription->patient_limit,
                    'doctors' => $user->subscription->doctor_limit,
                    'appointments' => $user->subscription->appointment_limit,
                ] : null,
            ],
            'permissions' => [
                'can_see_all_patients' => $user->role === 'admin',
                'should_filter_by_doctor' => $user->role !== 'admin' && $user->doctor,
                'accessible_modules' => $this->getAccessibleModules($user->role),
                'data_scope' => $user->role === 'admin' ? 'global' : 'filtered_by_role'
            ]
        ];
        
        return response()->json([
            'success' => true,
            'debug' => $debug
        ]);
    }

    private function getAccessibleModules($role)
    {
        $modules = [
            'admin' => ['patients', 'doctors', 'appointments', 'surgeries', 'exams', 'medications', 'invoices', 'clinics', 'users'],
            'doctor' => ['patients', 'appointments', 'surgeries', 'exams', 'medications'],
            'nurse' => ['patients', 'appointments', 'medications'],
            'receptionist' => ['patients', 'appointments'],
            'lab_technician' => ['exams', 'patients'],
            'accountant' => ['invoices', 'patients'],
        ];
        
        return $modules[$role] ?? [];
    }

    private function getSurgerySuccessRate()
    {
        $totalCompleted = Surgery::where('status', 'completed')->count();
        if ($totalCompleted === 0) {
            return 0;
        }
        
        // Assuming all completed surgeries are successful for now
        // In a real scenario, you might have a success field
        return 100;
    }
}
