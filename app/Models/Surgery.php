<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surgery extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'main_surgeon_id',
        'assistant_surgeons',
        'date_time',
        'estimated_duration',
        'surgery_type',
        'operating_room',
        'anesthesia_type',
        'required_equipment',
        'preop_notes',
        'status',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'estimated_duration' => 'integer',
        'assistant_surgeons' => 'array',
        'required_equipment' => 'array',
    ];

    /**
     * Get the patient that owns the surgery.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the main surgeon for the surgery.
     */
    public function mainSurgeon()
    {
        return $this->belongsTo(Doctor::class, 'main_surgeon_id');
    }

    /**
     * Check if surgery is scheduled
     */
    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if surgery is today
     */
    public function isToday()
    {
        return $this->date_time->isToday();
    }
}
