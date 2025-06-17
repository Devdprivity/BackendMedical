<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_exam_id',
        'performed_date',
        'reported_by',
        'values',
        'reference_values',
        'interpretation',
        'attachments',
        'status',
    ];

    protected $casts = [
        'performed_date' => 'datetime',
        'values' => 'array',
        'reference_values' => 'array',
        'attachments' => 'array',
    ];

    /**
     * Get the medical exam that owns the result.
     */
    public function medicalExam()
    {
        return $this->belongsTo(MedicalExam::class);
    }

    /**
     * Get the doctor that reported the result.
     */
    public function reportedBy()
    {
        return $this->belongsTo(Doctor::class, 'reported_by');
    }

    /**
     * Check if result requires attention
     */
    public function requiresAttention()
    {
        return $this->status === 'requires_attention';
    }

    /**
     * Check if result is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }
}
