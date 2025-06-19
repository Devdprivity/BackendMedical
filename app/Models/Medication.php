<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'clinic_id',
        'commercial_name',
        'generic_name',
        'manufacturer',
        'barcode',
        'category',
        'presentation',
        'concentration',
        'administration_route',
        'requires_prescription',
        'controlled',
        'current_stock',
        'min_stock',
        'max_stock',
        'location',
        'expiration_date',
        'lot_number',
        'unit_cost',
        'sale_price',
        'status',
        'indications',
        'contraindications',
        'side_effects',
        'drug_interactions',
        'storage_notes',
    ];

    protected $casts = [
        'requires_prescription' => 'boolean',
        'controlled' => 'boolean',
        'current_stock' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'expiration_date' => 'date',
        'unit_cost' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    /**
     * Get the inventory movements for this medication.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Check if medication is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if medication is low in stock
     */
    public function isLowStock()
    {
        return $this->current_stock <= $this->min_stock;
    }

    /**
     * Check if medication is expired
     */
    public function isExpired()
    {
        return $this->expiration_date < now();
    }

    /**
     * Check if medication is near expiration (within 30 days)
     */
    public function isNearExpiration()
    {
        return $this->expiration_date <= now()->addDays(30);
    }

    /**
     * Get the user who created this medication.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the clinic that owns this medication.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
