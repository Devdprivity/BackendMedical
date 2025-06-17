<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'medical_director',
        'foundation_year',
        'specialties',
        'schedule',
        'emergency_services',
        'status',
        'description',
    ];

    protected $casts = [
        'specialties' => 'array',
        'schedule' => 'array',
        'emergency_services' => 'boolean',
        'foundation_year' => 'integer',
    ];

    /**
     * Get the doctors associated with the clinic.
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_clinic')
                   ->withPivot('status', 'schedule')
                   ->withTimestamps();
    }

    /**
     * Get the patients that prefer this clinic.
     */
    public function patients()
    {
        return $this->hasMany(Patient::class, 'preferred_clinic_id');
    }

    /**
     * Get the appointments for this clinic.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Check if clinic is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get active doctors for this clinic
     */
    public function activeDoctors()
    {
        return $this->doctors()->wherePivot('status', 'active');
    }
}
