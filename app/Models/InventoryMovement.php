<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'reference_type',
        'reference_id',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'new_stock' => 'integer',
        'reference_id' => 'integer',
    ];

    /**
     * Get the medication that owns the movement.
     */
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Get the user that performed the movement.
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Check if movement is an input
     */
    public function isInput()
    {
        return $this->type === 'in';
    }

    /**
     * Check if movement is an output
     */
    public function isOutput()
    {
        return $this->type === 'out';
    }
}
