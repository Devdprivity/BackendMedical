<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'payment_method',
        'payment_id',
        'amount_paid',
        'billing_cycle',
        'current_doctors_count',
        'current_patients_count',
        'current_appointments_this_month',
        'current_locations_count',
        'current_staff_count',
        'last_monthly_reset',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'last_monthly_reset' => 'date',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan.
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at > now();
    }

    /**
     * Check if subscription is in trial.
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at > now();
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->ends_at <= now() || $this->status === 'expired';
    }

    /**
     * Check if subscription is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get days remaining in subscription.
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->ends_at, false));
    }

    /**
     * Get trial hours remaining.
     */
    public function getTrialHoursRemainingAttribute(): int
    {
        if (!$this->trial_ends_at || $this->trial_ends_at <= now()) {
            return 0;
        }

        return max(0, now()->diffInHours($this->trial_ends_at, false));
    }

    /**
     * Get trial days remaining.
     */
    public function getTrialDaysRemainingAttribute(): int
    {
        if (!$this->trial_ends_at || $this->trial_ends_at <= now()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->trial_ends_at, false));
    }

    /**
     * Check if user can add more doctors.
     */
    public function canAddDoctor(): bool
    {
        if ($this->plan->isUnlimited('max_doctors')) {
            return true;
        }

        return $this->current_doctors_count < $this->plan->max_doctors;
    }

    /**
     * Check if user can add more patients.
     */
    public function canAddPatient(): bool
    {
        if ($this->plan->isUnlimited('max_patients')) {
            return true;
        }

        return $this->current_patients_count < $this->plan->max_patients;
    }

    /**
     * Check if user can add more appointments this month.
     */
    public function canAddAppointment(): bool
    {
        $this->resetMonthlyCountersIfNeeded();

        if ($this->plan->isUnlimited('max_appointments_per_month')) {
            return true;
        }

        return $this->current_appointments_this_month < $this->plan->max_appointments_per_month;
    }

    /**
     * Check if user can add more staff.
     */
    public function canAddStaff(): bool
    {
        if ($this->plan->isUnlimited('max_staff')) {
            return true;
        }

        return $this->current_staff_count < $this->plan->max_staff;
    }

    /**
     * Increment usage counter.
     */
    public function incrementUsage(string $type): void
    {
        $field = 'current_' . $type . '_count';
        
        if ($type === 'appointments') {
            $this->resetMonthlyCountersIfNeeded();
            $field = 'current_appointments_this_month';
        }

        $this->increment($field);
    }

    /**
     * Decrement usage counter.
     */
    public function decrementUsage(string $type): void
    {
        $field = 'current_' . $type . '_count';
        
        if ($type === 'appointments') {
            $field = 'current_appointments_this_month';
        }

        $this->decrement($field);
    }

    /**
     * Reset monthly counters if needed.
     */
    public function resetMonthlyCountersIfNeeded(): void
    {
        $currentMonth = now()->format('Y-m');
        $lastReset = $this->last_monthly_reset ? $this->last_monthly_reset->format('Y-m') : null;

        if ($lastReset !== $currentMonth) {
            $this->update([
                'current_appointments_this_month' => 0,
                'last_monthly_reset' => now()->startOfMonth(),
            ]);
        }
    }

    /**
     * Cancel subscription.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Activate subscription.
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'cancelled_at' => null,
        ]);
    }

    /**
     * Expire subscription.
     */
    public function expire(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('ends_at', '>', now());
    }

    /**
     * Scope for trial subscriptions.
     */
    public function scopeTrial($query)
    {
        return $query->where('status', 'trial')
                     ->where('trial_ends_at', '>', now());
    }

    /**
     * Scope for expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('ends_at', '<=', now())
              ->orWhere('status', 'expired');
        });
    }
}
