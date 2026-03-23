<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'specialty',
        'license_number',
        'email',
        'phone',
        'emergency_phone',
        'address',
        'experience_years',
        'education',
        'certifications',
        'languages',
        'status',
        'bio',
        'photo_url',
        'rating',
        'base_city',
        'base_clinic_id',
        'travels_to_locations',
        'preferred_cities',
    ];

    protected $casts = [
        'education' => 'array',
        'certifications' => 'array',
        'languages' => 'array',
        'preferred_cities' => 'array',
        'experience_years' => 'integer',
        'rating' => 'decimal:2',
        'travels_to_locations' => 'boolean',
    ];

    /**
     * Get the user that owns the doctor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the clinics associated with the doctor.
     */
    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'doctor_clinic')
                   ->withPivot('status', 'schedule')
                   ->withTimestamps();
    }

    /**
     * Get the appointments for the doctor.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the surgeries where the doctor is the main surgeon.
     */
    public function surgeries()
    {
        return $this->hasMany(Surgery::class, 'main_surgeon_id');
    }

    /**
     * Get the medical exams requested by the doctor.
     */
    public function requestedExams()
    {
        return $this->hasMany(MedicalExam::class, 'requesting_doctor_id');
    }

    /**
     * Get the exam results reported by the doctor.
     */
    public function reportedResults()
    {
        return $this->hasMany(ExamResult::class, 'reported_by');
    }

    /**
     * Get the treatments prescribed by the doctor.
     */
    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Get active treatments prescribed by the doctor.
     */
    public function activeTreatments()
    {
        return $this->treatments()->active();
    }

    /**
     * Get current treatments prescribed by the doctor.
     */
    public function currentTreatments()
    {
        return $this->treatments()->current();
    }

    /**
     * Get doctor-patient relationships.
     */
    public function patientRelationships()
    {
        return $this->hasMany(DoctorPatientRelationship::class);
    }

    /**
     * Get active patient relationships.
     */
    public function activePatientRelationships()
    {
        return $this->patientRelationships()->active()->current();
    }

    /**
     * Get all patients associated with this doctor.
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'doctor_patient_relationships')
                    ->withPivot([
                        'relationship_type',
                        'started_at',
                        'ended_at',
                        'status',
                        'notes',
                        'permissions'
                    ])
                    ->wherePivot('status', 'active')
                    ->withTimestamps();
    }

    /**
     * Get primary patients (where doctor is primary care physician).
     */
    public function primaryPatients()
    {
        return $this->patients()->wherePivot('relationship_type', 'primary');
    }

    /**
     * Check if doctor can treat specific patient.
     */
    public function canTreatPatient($patientId)
    {
        return $this->patientRelationships()
                    ->where('patient_id', $patientId)
                    ->active()
                    ->current()
                    ->exists();
    }

    /**
     * Check if doctor has specific permission for patient.
     */
    public function hasPatientPermission($patientId, $permission)
    {
        $relationship = $this->patientRelationships()
                             ->where('patient_id', $patientId)
                             ->active()
                             ->current()
                             ->first();

        return $relationship && $relationship->hasPermission($permission);
    }

    /**
     * Check if doctor is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get today's appointments
     */
    public function todaysAppointments()
    {
        return $this->appointments()->whereDate('date_time', today());
    }

    /**
     * Ensure doctor-patient relationship exists, create if not
     */
    public function ensurePatientRelationship($patientId, $relationshipType = 'primary')
    {
        $existingRelationship = $this->patientRelationships()
            ->where('patient_id', $patientId)
            ->where('status', 'active')
            ->current()
            ->first();

        if (!$existingRelationship) {
            // Get clinic_id safely
            $user = $this->user()->first();
            $clinicId = $user ? $user->clinic_id : null;

            return \App\Models\DoctorPatientRelationship::create([
                'doctor_id' => $this->id,
                'patient_id' => $patientId,
                'clinic_id' => $clinicId,
                'relationship_type' => $relationshipType,
                'started_at' => now()->toDateString(),
                'status' => 'active',
                'notes' => 'Relación creada automáticamente',
                'permissions' => json_encode($this->getDefaultPermissions($relationshipType)),
            ]);
        }

        return $existingRelationship;
    }

    /**
     * Get default permissions for relationship type
     */
    private function getDefaultPermissions($relationshipType)
    {
        switch ($relationshipType) {
            case 'primary':
                return ['view_history', 'prescribe', 'update_records', 'schedule_appointments', 'emergency_access'];
            case 'consulting':
                return ['view_history', 'prescribe', 'update_records'];
            case 'specialist':
                return ['view_history', 'prescribe', 'update_specialty_records'];
            case 'emergency':
                return ['view_history', 'emergency_prescribe', 'emergency_access'];
            default:
                return ['view_history'];
        }
    }

    /**
     * Create automatic relationship when doctor creates a patient
     */
    public function createPatientRelationship($patientId)
    {
        return $this->ensurePatientRelationship($patientId, 'primary');
    }

    /**
     * Get doctor schedules
     */
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /**
     * Get active schedules
     */
    public function activeSchedules()
    {
        return $this->schedules()->active()->availableForBooking();
    }

    /**
     * Get base clinic relationship
     */
    public function baseClinic()
    {
        return $this->belongsTo(Clinic::class, 'base_clinic_id');
    }

    /**
     * Get schedules for a specific city
     */
    public function schedulesInCity($city)
    {
        return $this->schedules()->inCity($city)->active();
    }

    /**
     * Check if doctor has schedule in a specific city
     */
    public function hasScheduleInCity($city)
    {
        return $this->schedules()->inCity($city)->active()->exists();
    }

    /**
     * Get all cities where doctor has schedules
     */
    public function getCitiesWithSchedules()
    {
        return $this->schedules()
            ->active()
            ->distinct()
            ->pluck('city')
            ->toArray();
    }
}
