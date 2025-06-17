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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $stats = [
            // General statistics
            'patients' => [
                'total' => Patient::count(),
                'active' => Patient::where('status', 'active')->count(),
                'with_pending_appointments' => Patient::whereHas('appointments', function($query) {
                    $query->where('status', 'scheduled');
                })->count(),
                'with_allergies' => Patient::whereHas('medicalHistory', function($query) {
                    $query->whereNotNull('allergies');
                })->count(),
            ],
            
            'appointments' => [
                'today' => Appointment::whereDate('date_time', today())->count(),
                'pending' => Appointment::where('status', 'scheduled')->count(),
                'completed' => Appointment::where('status', 'completed')->count(),
                'cancelled' => Appointment::where('status', 'cancelled')->count(),
            ],
            
            'surgeries' => [
                'scheduled' => Surgery::where('status', 'scheduled')->count(),
                'today' => Surgery::whereDate('date_time', today())->count(),
                'completed' => Surgery::where('status', 'completed')->count(),
                'success_rate' => $this->getSurgerySuccessRate(),
            ],
            
            'exams' => [
                'pending' => MedicalExam::where('status', 'scheduled')->count(),
                'completed' => MedicalExam::where('status', 'completed')->count(),
                'results_pending' => MedicalExam::whereDoesntHave('result')->count(),
            ],
            
            'invoices' => [
                'pending' => Invoice::where('payment_status', 'pending')->count(),
                'paid' => Invoice::where('payment_status', 'paid')->count(),
                'overdue' => Invoice::where('payment_status', 'overdue')->count(),
                'today_total' => Invoice::whereDate('created_at', today())->sum('total'),
            ],
            
            'clinics' => [
                'total' => Clinic::count(),
                'active' => Clinic::where('status', 'active')->count(),
                'total_doctors' => Doctor::where('status', 'active')->count(),
            ],
            
            'medications' => [
                'total' => Medication::count(),
                'low_stock' => Medication::whereRaw('current_stock <= min_stock')->count(),
                'expiring' => Medication::where('expiration_date', '<=', now()->addDays(30))->count(),
                'inventory_value' => Medication::selectRaw('SUM(current_stock * unit_cost) as value')->value('value') ?? 0,
            ],
        ];

        return response()->json($stats);
    }

    public function recentActivity(Request $request)
    {
        $recentActivity = [
            'appointments' => Appointment::with(['patient', 'doctor', 'clinic'])
                ->latest()
                ->take(5)
                ->get(),
            
            'patients' => Patient::with('preferredClinic')
                ->latest()
                ->take(5)
                ->get(),
            
            'invoices' => Invoice::with('patient')
                ->where('payment_status', 'paid')
                ->latest()
                ->take(5)
                ->get(),
                
            'exams' => MedicalExam::with(['patient', 'requestingDoctor'])
                ->where('status', 'completed')
                ->latest()
                ->take(5)
                ->get(),
        ];

        // Add alerts
        $alerts = [
            'medications_expiring' => Medication::where('expiration_date', '<=', now()->addDays(7))
                ->count(),
            'overdue_payments' => Invoice::where('payment_status', 'overdue')
                ->count(),
            'critical_results' => DB::table('exam_results')
                ->where('status', 'requires_attention')
                ->count(),
        ];

        return response()->json([
            'activity' => $recentActivity,
            'alerts' => $alerts,
        ]);
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
