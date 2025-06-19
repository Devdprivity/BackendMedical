<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class VideoCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'room_name',
        'room_url',
        'status',
        'started_at',
        'ended_at',
        'duration_minutes',
        'participants',
        'notes',
        'recording_enabled',
        'recording_url',
        'created_by',
        'is_instant'
    ];

    protected $casts = [
        'participants' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'recording_enabled' => 'boolean',
        'is_instant' => 'boolean'
    ];

    /**
     * Get the appointment that owns the video call
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the user who created the video call
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate a unique room name for the video call
     */
    public static function generateRoomName(int $appointmentId): string
    {
        return 'medicare-consultation-' . $appointmentId . '-' . time();
    }

    /**
     * Generate the Jitsi Meet URL
     */
    public static function generateJitsiUrl(string $roomName): string
    {
        return 'https://meet.jit.si/' . $roomName;
    }

    /**
     * Start the video call session
     */
    public function start(): void
    {
        $this->update([
            'status' => 'active',
            'started_at' => now()
        ]);
    }

    /**
     * End the video call session
     */
    public function end(): void
    {
        $startTime = $this->started_at;
        $endTime = now();
        
        $duration = $startTime ? $startTime->diffInMinutes($endTime) : 0;
        
        $this->update([
            'status' => 'completed',
            'ended_at' => $endTime,
            'duration_minutes' => $duration
        ]);
    }

    /**
     * Check if the video call is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the video call can be started
     */
    public function canStart(): bool
    {
        return in_array($this->status, ['pending', 'cancelled']);
    }

    /**
     * Add a participant to the call
     */
    public function addParticipant(array $participant): void
    {
        $participants = $this->participants ?? [];
        $participants[] = array_merge($participant, [
            'joined_at' => now()->toISOString()
        ]);
        
        $this->update(['participants' => $participants]);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_minutes) {
            return '0 min';
        }
        
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'min';
        }
        
        return $minutes . 'min';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'active' => 'success',
            'completed' => 'info',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'active' => 'En curso',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            default => 'Desconocido'
        };
    }
}
