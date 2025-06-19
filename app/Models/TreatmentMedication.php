<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentMedication extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_id',
        'medication_id',
        'dosage',
        'frequency',
        'duration',
        'administration_instructions',
        'quantity_prescribed',
        'quantity_dispensed',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity_prescribed' => 'integer',
        'quantity_dispensed' => 'integer',
    ];

    /**
     * Get the treatment that owns this medication record.
     */
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    /**
     * Get the medication that is prescribed.
     */
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Check if medication is pending dispensing
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if medication has been dispensed
     */
    public function isDispensed()
    {
        return $this->status === 'dispensed';
    }

    /**
     * Check if medication treatment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Get remaining quantity to dispense
     */
    public function getRemainingQuantityAttribute()
    {
        return max(0, $this->quantity_prescribed - $this->quantity_dispensed);
    }

    /**
     * Get dispensing progress percentage
     */
    public function getDispensingProgressAttribute()
    {
        if ($this->quantity_prescribed <= 0) {
            return 0;
        }
        
        return round(($this->quantity_dispensed / $this->quantity_prescribed) * 100);
    }

    /**
     * Check if fully dispensed
     */
    public function isFullyDispensed()
    {
        return $this->quantity_dispensed >= $this->quantity_prescribed;
    }

    /**
     * Dispense medication
     */
    public function dispense($quantity, $notes = null)
    {
        $newQuantityDispensed = min(
            $this->quantity_prescribed,
            $this->quantity_dispensed + $quantity
        );
        
        $this->update([
            'quantity_dispensed' => $newQuantityDispensed,
            'status' => $this->isFullyDispensed() ? 'dispensed' : 'pending',
            'notes' => $notes ? ($this->notes . "\n" . $notes) : $this->notes
        ]);
        
        return $this;
    }

    /**
     * Scope for pending medications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for dispensed medications
     */
    public function scopeDispensed($query)
    {
        return $query->where('status', 'dispensed');
    }

    /**
     * Scope for completed medications
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
} 