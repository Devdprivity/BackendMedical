<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'clinic_id',
        'title',
        'description',
        'type',
        'priority',
        'start_date',
        'end_date',
        'duration_days',
        'instructions',
        'medications',
        'precautions',
        'side_effects_to_watch',
        'status',
        'notes',
        'shared_via',
        'last_shared_at',
        'qr_code',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'medications' => 'array',
        'shared_via' => 'array',
        'last_shared_at' => 'datetime',
        'duration_days' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($treatment) {
            if (empty($treatment->qr_code)) {
                $treatment->qr_code = Str::uuid();
            }
        });
    }

    /**
     * Get the patient that owns the treatment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor that prescribed the treatment.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the clinic where the treatment was prescribed.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the medications associated with this treatment.
     */
    public function treatmentMedications()
    {
        return $this->hasMany(TreatmentMedication::class);
    }

    /**
     * Get medications through the pivot table.
     */
    public function medicationsDetailed()
    {
        return $this->belongsToMany(Medication::class, 'treatment_medications')
                    ->withPivot([
                        'dosage',
                        'frequency',
                        'duration',
                        'administration_instructions',
                        'quantity_prescribed',
                        'quantity_dispensed',
                        'status',
                        'notes'
                    ])
                    ->withTimestamps();
    }

    /**
     * Check if treatment is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if treatment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if treatment is current (within date range)
     */
    public function isCurrent()
    {
        $now = Carbon::now()->toDateString();
        return $this->start_date <= $now && 
               ($this->end_date === null || $this->end_date >= $now);
    }

    /**
     * Check if treatment is overdue
     */
    public function isOverdue()
    {
        return $this->end_date && 
               Carbon::parse($this->end_date)->isPast() && 
               $this->status === 'active';
    }

    /**
     * Get treatment duration in days
     */
    public function getDurationAttribute()
    {
        if ($this->duration_days) {
            return $this->duration_days;
        }
        
        if ($this->start_date && $this->end_date) {
            return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date));
        }
        
        return null;
    }

    /**
     * Get remaining days of treatment
     */
    public function getRemainingDaysAttribute()
    {
        if (!$this->end_date || $this->status !== 'active') {
            return null;
        }
        
        $remaining = Carbon::now()->diffInDays(Carbon::parse($this->end_date), false);
        return max(0, $remaining);
    }

    /**
     * Get progress percentage
     */
    public function getProgressAttribute()
    {
        if (!$this->duration) {
            return 0;
        }
        
        $elapsed = Carbon::parse($this->start_date)->diffInDays(Carbon::now());
        return min(100, round(($elapsed / $this->duration) * 100));
    }

    /**
     * Mark as shared via specific method
     */
    public function markAsShared($method, $details = [])
    {
        $sharedVia = $this->shared_via ?? [];
        $sharedVia[] = [
            'method' => $method,
            'timestamp' => now(),
            'details' => $details
        ];
        
        $this->update([
            'shared_via' => $sharedVia,
            'last_shared_at' => now()
        ]);
    }

    /**
     * Generate QR code URL
     */
    public function getQrUrlAttribute()
    {
        return route('treatments.public', ['qr' => $this->qr_code]);
    }

    /**
     * Generate WhatsApp share URL
     */
    public function getWhatsappUrlAttribute()
    {
        $message = "🏥 *Tratamiento Médico*\n\n";
        $message .= "📋 *{$this->title}*\n";
        $message .= "👨‍⚕️ Dr. {$this->doctor->name}\n";
        $message .= "📅 Desde: {$this->start_date->format('d/m/Y')}\n";
        if ($this->end_date) {
            $message .= "📅 Hasta: {$this->end_date->format('d/m/Y')}\n";
        }
        $message .= "\n📱 Ver detalles completos:\n{$this->qr_url}";
        
        return 'https://wa.me/?text=' . urlencode($message);
    }

    /**
     * Scope for active treatments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for current treatments (within date range)
     */
    public function scopeCurrent($query)
    {
        $now = Carbon::now()->toDateString();
        return $query->where('start_date', '<=', $now)
                    ->where(function($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    });
    }

    /**
     * Scope for overdue treatments
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '<', Carbon::now()->toDateString());
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
} 