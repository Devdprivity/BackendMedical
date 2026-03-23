<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'clinic_id',
        'date_time',
        'duration',
        'type',
        'reason',
        'notes',
        'status',
        'confirmation_required',
        'confirmation_sent_at',
        'confirmation_status',
        'reminder_hours_before',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'duration' => 'integer',
        'confirmation_required' => 'boolean',
        'confirmation_sent_at' => 'datetime',
        'reminder_hours_before' => 'integer',
    ];

    /**
     * Get the patient that owns the appointment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor that owns the appointment.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the clinic that owns the appointment.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the video call associated with this appointment.
     */
    public function videoCall()
    {
        return $this->hasOne(VideoCall::class);
    }

    /**
     * Get the payment associated with this appointment.
     */
    public function payment()
    {
        return $this->hasOne(AppointmentPayment::class);
    }

    /**
     * Check if appointment is scheduled
     */
    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if appointment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if appointment is today
     */
    public function isToday()
    {
        return $this->date_time->isToday();
    }

    /**
     * Check if appointment has video call capability
     */
    public function hasVideoCall()
    {
        return $this->videoCall !== null;
    }

    /**
     * Check if video call can be started (appointment is today and within time range)
     */
    public function canStartVideoCall()
    {
        if (!$this->isScheduled()) {
            return false;
        }

        $now = now();
        $appointmentTime = $this->date_time;
        
        // Allow starting 15 minutes before and up to 60 minutes after appointment time
        $startWindow = $appointmentTime->subMinutes(15);
        $endWindow = $appointmentTime->addMinutes(60);
        
        return $now->between($startWindow, $endWindow);
    }

    /**
     * Check if appointment is a video consultation
     */
    public function isVideoConsultation()
    {
        return $this->type === 'video_consultation' || $this->type === 'online';
    }
}
