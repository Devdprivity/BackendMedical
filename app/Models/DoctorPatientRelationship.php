<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DoctorPatientRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'clinic_id',
        'relationship_type',
        'started_at',
        'ended_at',
        'status',
        'notes',
        'permissions',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
        'permissions' => 'array',
    ];

    /**
     * Get the doctor in this relationship.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the patient in this relationship.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the clinic where this relationship exists.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Check if relationship is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if relationship is current (within date range)
     */
    public function isCurrent()
    {
        $now = Carbon::now()->toDateString();
        return $this->started_at <= $now && 
               ($this->ended_at === null || $this->ended_at >= $now);
    }

    /**
     * Check if doctor has specific permission
     */
    public function hasPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions) || $this->relationship_type === 'primary';
    }

    /**
     * Get default permissions based on relationship type
     */
    public function getDefaultPermissions()
    {
        switch ($this->relationship_type) {
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
     * Set default permissions if none are set
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($relationship) {
            if (empty($relationship->permissions)) {
                $relationship->permissions = $relationship->getDefaultPermissions();
            }
        });
    }

    /**
     * Scope for active relationships
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for current relationships (within date range)
     */
    public function scopeCurrent($query)
    {
        $now = Carbon::now()->toDateString();
        return $query->where('started_at', '<=', $now)
                    ->where(function($q) use ($now) {
                        $q->whereNull('ended_at')
                          ->orWhere('ended_at', '>=', $now);
                    });
    }

    /**
     * Scope by relationship type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('relationship_type', $type);
    }

    /**
     * Scope for primary doctors
     */
    public function scopePrimary($query)
    {
        return $query->where('relationship_type', 'primary');
    }
} 