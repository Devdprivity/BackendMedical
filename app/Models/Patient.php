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
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Get the preferred clinic for the patient.
     */
    public function preferredClinic()
    {
        return $this->belongsTo(Clinic::class, 'preferred_clinic_id');
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
