<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'patient_id',
        'issue_date',
        'due_date',
        'items',
        'subtotal',
        'tax',
        'total',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the patient that owns the invoice.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue()
    {
        return $this->payment_status === 'overdue' || 
               ($this->payment_status === 'pending' && $this->due_date < now());
    }
}
