<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dni',
        'birth_date',
        'gender',
        'blood_type',
        'address',
        'phone',
        'email',
        'status',
        'preferred_clinic_id',
        'created_by',
        'whatsapp_number',
        'whatsapp_opt_in',
        'prefers_whatsapp',
        'whatsapp_opt_in_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'whatsapp_opt_in' => 'boolean',
        'prefers_whatsapp' => 'boolean',
        'whatsapp_opt_in_date' => 'datetime',
    ];

    /**
     * Get the preferred clinic for the patient.
     */
    public function preferredClinic()
    {
        return $this->belongsTo(Clinic::class, 'preferred_clinic_id');
    }

    /**
     * Get the user who created this patient.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the emergency contact for the patient.
     */
    public function emergencyContact()
    {
        return $this->hasOne(EmergencyContact::class);
    }

    /**
     * Get the medical history for the patient.
     */
    public function medicalHistory()
    {
        return $this->hasOne(MedicalHistory::class);
    }

    /**
     * Get the vital signs for the patient.
     */
    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the surgeries for the patient.
     */
    public function surgeries()
    {
        return $this->hasMany(Surgery::class);
    }

    /**
     * Get the medical exams for the patient.
     */
    public function medicalExams()
    {
        return $this->hasMany(MedicalExam::class);
    }

    /**
     * Get the invoices for the patient.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the treatments for the patient.
     */
    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Get active treatments for the patient.
     */
    public function activeTreatments()
    {
        return $this->treatments()->active();
    }

    /**
     * Get current treatments (within date range).
     */
    public function currentTreatments()
    {
        return $this->treatments()->current();
    }

    /**
     * Get doctor-patient relationships.
     */
    public function doctorRelationships()
    {
        return $this->hasMany(DoctorPatientRelationship::class);
    }

    /**
     * Get active doctor relationships.
     */
    public function activeDoctorRelationships()
    {
        return $this->doctorRelationships()->active()->current();
    }

    /**
     * Get primary doctor.
     */
    public function primaryDoctor()
    {
        return $this->doctorRelationships()
                    ->active()
                    ->current()
                    ->primary()
                    ->with('doctor')
                    ->first()?->doctor;
    }

    /**
     * Get all doctors associated with this patient.
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_patient_relationships')
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
     * Calculate the patient's age
     */
    public function getAgeAttribute()
    {
        return Carbon::parse($this->birth_date)->age;
    }

    /**
     * Check if patient is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get the latest vital signs
     */
    public function latestVitalSigns()
    {
        return $this->vitalSigns()->latest('measured_at')->first();
    }
}
