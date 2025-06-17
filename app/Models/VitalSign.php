<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalSign extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'weight',
        'height',
        'blood_pressure',
        'heart_rate',
        'temperature',
        'measured_at',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'heart_rate' => 'integer',
        'temperature' => 'decimal:2',
        'measured_at' => 'datetime',
    ];

    /**
     * Get the patient that owns the vital sign.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Calculate BMI if height and weight are present
     */
    public function getBmiAttribute()
    {
        if ($this->weight && $this->height) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }
}
