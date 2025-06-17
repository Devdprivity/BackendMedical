<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'allergies',
        'conditions',
        'medications',
        'surgeries',
    ];

    protected $casts = [
        'allergies' => 'array',
        'conditions' => 'array',
        'medications' => 'array',
        'surgeries' => 'array',
    ];

    /**
     * Get the patient that owns the medical history.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
