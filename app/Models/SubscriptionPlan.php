<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'is_active',
        'is_free',
        'trial_days',
        'max_doctors',
        'max_patients',
        'max_appointments_per_month',
        'max_locations',
        'max_staff',
        'features',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_free' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
    ];

    /**
     * Get the subscriptions for this plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get active subscriptions for this plan.
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(UserSubscription::class)->where('status', 'active');
    }

    /**
     * Check if plan has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Get yearly savings percentage.
     */
    public function getYearlySavingsAttribute(): float
    {
        if ($this->price_monthly <= 0 || $this->price_yearly <= 0) {
            return 0;
        }
        
        $monthlyTotal = $this->price_monthly * 12;
        return round((($monthlyTotal - $this->price_yearly) / $monthlyTotal) * 100, 1);
    }

    /**
     * Get formatted monthly price.
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return $this->price_monthly > 0 ? '$' . number_format($this->price_monthly, 0) : 'Gratis';
    }

    /**
     * Get formatted yearly price.
     */
    public function getFormattedYearlyPriceAttribute(): string
    {
        return $this->price_yearly > 0 ? '$' . number_format($this->price_yearly, 0) : 'Gratis';
    }

    /**
     * Check if plan is unlimited for a specific limit.
     */
    public function isUnlimited(string $limit): bool
    {
        return is_null($this->{$limit});
    }

    /**
     * Get limit display text.
     */
    public function getLimitDisplay(string $limit): string
    {
        $value = $this->{$limit};
        
        if (is_null($value)) {
            return 'Ilimitado';
        }
        
        return number_format($value);
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for free plans.
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope ordered by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price_monthly');
    }
}
