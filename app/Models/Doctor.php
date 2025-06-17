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
    ];

    protected $casts = [
        'education' => 'array',
        'certifications' => 'array',
        'languages' => 'array',
        'experience_years' => 'integer',
        'rating' => 'decimal:2',
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
}
