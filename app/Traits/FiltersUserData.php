<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait FiltersUserData
{
    /**
     * Apply user-specific filters based on role and ownership
     */
    protected function applyUserFilters(Builder $query, Request $request, string $entityType = null): Builder
    {
        $user = auth()->user();

        // Admin sees everything
        if ($user->role === 'admin') {
            return $query;
        }

        // Apply role-specific filters
        switch ($user->role) {
            case 'doctor':
                return $this->applyDoctorFilters($query, $user, $entityType);

            case 'nurse':
                return $this->applyNurseFilters($query, $user, $entityType);

            case 'receptionist':
                return $this->applyReceptionistFilters($query, $user, $entityType);

            case 'lab_technician':
                return $this->applyLabTechFilters($query, $user, $entityType);

            case 'accountant':
                return $this->applyAccountantFilters($query, $user, $entityType);

            default:
                // Unknown role - restrict to nothing
                return $query->whereRaw('1 = 0');
        }
    }

    /**
     * Apply doctor-specific filters
     */
    protected function applyDoctorFilters(Builder $query, $user, string $entityType = null): Builder
    {
        switch ($entityType) {
            case 'patients':
                // Doctor can only see patients with whom they have an active doctor-patient relationship
                if (!$user->doctor) {
                    return $query->whereRaw('1 = 0'); // No doctor record = no patients
                }

                return $query->whereHas('doctorRelationships', function($relationships) use ($user) {
                    $relationships->where('doctor_id', $user->doctor->id)
                                  ->where('status', 'active')
                                  ->current();
                });

            case 'appointments':
                // Doctor sees only their appointments
                if (!$user->doctor) {
                    return $query->whereRaw('1 = 0'); // No doctor record = no appointments
                }
                return $query->where('doctor_id', $user->doctor->id);

            case 'surgeries':
                // Doctor sees only surgeries where they are main surgeon
                if (!$user->doctor) {
                    return $query->whereRaw('1 = 0');
                }
                return $query->where('main_surgeon_id', $user->doctor->id);

            case 'exams':
                // Doctor sees only exams they requested
                if (!$user->doctor) {
                    return $query->whereRaw('1 = 0');
                }
                return $query->where('requesting_doctor_id', $user->doctor->id);

            case 'medications':
                // Doctor sees medications they created or from their clinic
                return $query->where(function($q) use ($user) {
                    $q->where('created_by', $user->id);
                    if ($user->clinic_id) {
                        $q->orWhere('clinic_id', $user->clinic_id);
                    }
                })->where('current_stock', '>', 0); // Only available medications

            case 'treatments':
                // Doctor sees only treatments they prescribed or for their patients
                if (!$user->doctor) {
                    return $query->where('created_by', $user->id); // Fallback to created by user
                }
                return $query->where(function($q) use ($user) {
                    $q->where('doctor_id', $user->doctor->id)
                      ->orWhereHas('patient.doctorRelationships', function($rel) use ($user) {
                          $rel->where('doctor_id', $user->doctor->id)
                              ->where('status', 'active');
                      });
                });

            case 'relationships':
                // Doctor sees only their patient relationships
                if (!$user->doctor) {
                    return $query->whereRaw('1 = 0');
                }
                return $query->where('doctor_id', $user->doctor->id);

            default:
                return $query;
        }
    }

    /**
     * Apply nurse-specific filters
     */
    protected function applyNurseFilters(Builder $query, $user, string $entityType = null): Builder
    {
        switch ($entityType) {
            case 'patients':
                // Nurse sees patients with appointments in next 2 days
                return $query->whereHas('appointments', function($q) {
                    $q->whereBetween('date_time', [now(), now()->addDays(2)]);
                });

            case 'appointments':
                // Nurse sees appointments for today and tomorrow
                return $query->whereBetween('date_time', [now()->startOfDay(), now()->addDay()->endOfDay()]);

            case 'medications':
                // Nurse sees medications from their clinic only
                return $query->where('clinic_id', $user->clinic_id)
                           ->where('current_stock', '>', 0); // Only available medications

            case 'treatments':
                // Nurse sees treatments for patients with recent appointments
                return $query->whereHas('patient.appointments', function($q) {
                    $q->whereBetween('date_time', [now()->subDays(7), now()->addDays(2)]);
                });

            default:
                return $query->whereRaw('1 = 0'); // Restrict other entities
        }
    }

    /**
     * Apply receptionist-specific filters
     */
    protected function applyReceptionistFilters(Builder $query, $user, string $entityType = null): Builder
    {
        switch ($entityType) {
            case 'patients':
                // Receptionist sees all patients (for scheduling)
                return $query;

            case 'appointments':
                // Receptionist sees appointments from 1 day ago to 7 days forward
                return $query->whereBetween('date_time', [
                    now()->subDay()->startOfDay(),
                    now()->addDays(7)->endOfDay()
                ]);

            default:
                return $query->whereRaw('1 = 0'); // Restrict other entities
        }
    }

    /**
     * Apply lab technician-specific filters
     */
    protected function applyLabTechFilters(Builder $query, $user, string $entityType = null): Builder
    {
        switch ($entityType) {
            case 'exams':
                // Lab tech sees all exams (for processing)
                return $query;

            case 'patients':
                // Lab tech sees patients with pending exams
                return $query->whereHas('medicalExams', function($q) {
                    $q->whereIn('status', ['scheduled', 'in_progress']);
                });

            default:
                return $query->whereRaw('1 = 0'); // Restrict other entities
        }
    }

    /**
     * Apply accountant-specific filters
     */
    protected function applyAccountantFilters(Builder $query, $user, string $entityType = null): Builder
    {
        switch ($entityType) {
            case 'invoices':
                // Accountant sees all invoices
                return $query;

            case 'patients':
                // Accountant sees patients with invoices
                return $query->whereHas('invoices');

            default:
                return $query->whereRaw('1 = 0'); // Restrict other entities
        }
    }

    /**
     * Check if user can perform action on entity
     */
    protected function canUserAccess(string $action, string $entityType): bool
    {
        $user = auth()->user();

        // Admin can do everything
        if ($user->role === 'admin') {
            return true;
        }

        // Check subscription restrictions for medications
        if ($entityType === 'medications') {
            $subscription = $user->currentSubscription;
            if (!$subscription || !$subscription->plan) {
                return false; // No subscription = no access to medications
            }

            // Check if plan has inventory_management feature
            if (!$subscription->plan->hasFeature('inventory_management')) {
                return false; // Plan doesn't include medication management
            }
        }

        $permissions = [
            'doctor' => [
                'view' => ['patients', 'appointments', 'surgeries', 'exams', 'medications', 'treatments', 'relationships'],
                'create' => ['patients', 'appointments', 'exams', 'surgeries', 'treatments', 'relationships', 'medications'],
                'update' => ['patients', 'appointments', 'exams', 'surgeries', 'treatments', 'relationships', 'medications'],
                'delete' => ['appointments', 'treatments', 'medications']
            ],
            'nurse' => [
                'view' => ['patients', 'appointments', 'medications', 'treatments'],
                'create' => [],
                'update' => ['patients', 'medications'], // vital signs and medication stock
                'delete' => []
            ],
            'receptionist' => [
                'view' => ['patients', 'appointments', 'treatments'],
                'create' => ['patients', 'appointments'],
                'update' => ['patients', 'appointments'],
                'delete' => ['appointments']
            ],
            'lab_technician' => [
                'view' => ['exams', 'patients'],
                'create' => [],
                'update' => ['exams'], // results
                'delete' => []
            ],
            'accountant' => [
                'view' => ['invoices', 'patients'],
                'create' => ['invoices'],
                'update' => ['invoices'],
                'delete' => []
            ]
        ];

        return in_array($entityType, $permissions[$user->role][$action] ?? []);
    }

    /**
     * Apply subscription limits
     */
    protected function applySubscriptionLimits(Builder $query, string $entityType): Builder
    {
        $user = auth()->user();

        // Admin bypasses subscription limits
        if ($user->role === 'admin') {
            return $query;
        }

        $subscription = $user->currentSubscription;
        if (!$subscription) {
            // Free plan - very limited access
            switch ($entityType) {
                case 'patients':
                    return $query->limit(5);
                case 'appointments':
                    return $query->limit(10);
                default:
                    return $query->limit(3);
            }
        }

        // Apply subscription-specific limits
        switch ($entityType) {
            case 'patients':
                if ($subscription->patient_limit) {
                    return $query->limit($subscription->patient_limit);
                }
                break;

            case 'appointments':
                if ($subscription->appointment_limit) {
                    return $query->limit($subscription->appointment_limit);
                }
                break;
        }

        return $query;
    }
}