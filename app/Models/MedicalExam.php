<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'requesting_doctor_id',
        'exam_type',
        'scheduled_date',
        'laboratory_area',
        'preparation_required',
        'notes',
        'status',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
    ];

    /**
     * Get the patient that owns the exam.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the requesting doctor.
     */
    public function requestingDoctor()
    {
        return $this->belongsTo(Doctor::class, 'requesting_doctor_id');
    }

    /**
     * Get the exam result.
     */
    public function result()
    {
        return $this->hasOne(ExamResult::class);
    }

    /**
     * Check if exam is scheduled
     */
    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if exam is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}
