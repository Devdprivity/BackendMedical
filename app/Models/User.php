<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login',
        'google_id',
        'avatar',
        'booking_slug',
        'booking_enabled',
        'consultation_fee',
        'consultation_duration',
        'schedule_start',
        'schedule_end',
        'work_days',
        'break_start',
        'break_end',
        'bio',
        'specialty',
        'clinic_id',
        'location_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the doctor record associated with the user.
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * Get the user's subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the user's current subscription.
     */
    public function currentSubscription()
    {
        return $this->hasOne(UserSubscription::class)
                    ->where(function($query) {
                        $query->where('status', 'active')
                              ->orWhere('status', 'trial');
                    })
                    ->where('ends_at', '>', now())
                    ->latest();
    }

    /**
     * Get the user's active subscription.
     */
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
                    ->where('status', 'active')
                    ->where('ends_at', '>', now());
    }

    /**
     * Get the user's trial subscription.
     */
    public function trialSubscription()
    {
        return $this->hasOne(UserSubscription::class)
                    ->where('status', 'trial')
                    ->where('trial_ends_at', '>', now());
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->currentSubscription()->exists();
    }

    /**
     * Check if user is on trial.
     */
    public function isOnTrial(): bool
    {
        return $this->trialSubscription()->exists();
    }

    /**
     * Check if user can access a feature.
     */
    public function canAccessFeature(string $feature): bool
    {
        $subscription = $this->currentSubscription;
        
        if (!$subscription) {
            return false;
        }

        return $subscription->plan->hasFeature($feature);
    }

    /**
     * Check if user can perform an action based on limits.
     */
    public function canPerformAction(string $action): bool
    {
        $subscription = $this->currentSubscription;
        
        if (!$subscription) {
            return false;
        }

        switch ($action) {
            case 'add_doctor':
                return $subscription->canAddDoctor();
            case 'add_patient':
                return $subscription->canAddPatient();
            case 'add_appointment':
                return $subscription->canAddAppointment();
            case 'add_staff':
                return $subscription->canAddStaff();
            default:
                return false;
        }
    }

    /**
     * Get user's subscription status.
     */
    public function getSubscriptionStatusAttribute(): string
    {
        $subscription = $this->currentSubscription;
        
        if (!$subscription) {
            return 'none';
        }

        if ($subscription->isTrial()) {
            return 'trial';
        }

        if ($subscription->isActive()) {
            return 'active';
        }

        if ($subscription->isExpired()) {
            return 'expired';
        }

        return 'cancelled';
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if user registered with Google.
     */
    public function isGoogleUser(): bool
    {
        return !empty($this->google_id);
    }
}
