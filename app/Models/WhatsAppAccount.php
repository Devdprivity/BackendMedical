<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'phone_number',
        'display_name',
        'status',
        'type',
        'registration_date',
        'days_since_registration',
        'daily_message_limit',
        'hourly_message_limit',
        'messages_sent_today',
        'messages_sent_this_hour',
        'new_contacts_today',
        'response_rate',
        'total_messages_sent',
        'total_responses_received',
        'last_ban_date',
        'ban_count',
        'session_data',
        'api_credentials',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'last_ban_date' => 'datetime',
        'api_credentials' => 'array',
        'response_rate' => 'decimal:2',
        'days_since_registration' => 'integer',
        'daily_message_limit' => 'integer',
        'hourly_message_limit' => 'integer',
        'messages_sent_today' => 'integer',
        'messages_sent_this_hour' => 'integer',
        'new_contacts_today' => 'integer',
        'total_messages_sent' => 'integer',
        'total_responses_received' => 'integer',
        'ban_count' => 'integer',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function canSendMessage()
    {
        if ($this->status !== 'active') {
            return false;
        }

        $currentLimit = $this->getCurrentDailyLimit();

        return $this->messages_sent_today < $currentLimit
            && $this->messages_sent_this_hour < $this->hourly_message_limit;
    }

    public function isWarmingUp()
    {
        return $this->days_since_registration < 8;
    }

    public function getCurrentDailyLimit()
    {
        $days = $this->days_since_registration;

        if ($days === 1) return 10;
        if ($days <= 3) return 20;
        if ($days <= 7) return 50;

        return $this->daily_message_limit;
    }

    public function incrementMessageCount()
    {
        $this->increment('messages_sent_today');
        $this->increment('messages_sent_this_hour');
        $this->increment('total_messages_sent');
    }

    public function incrementResponseCount()
    {
        $this->increment('total_responses_received');
        $this->updateResponseRate();
    }

    public function updateResponseRate()
    {
        if ($this->total_messages_sent > 0) {
            $this->response_rate = ($this->total_responses_received / $this->total_messages_sent) * 100;
            $this->save();
        }
    }

    public static function resetDailyCounters()
    {
        self::query()->update([
            'messages_sent_today' => 0,
            'new_contacts_today' => 0
        ]);
    }

    public static function resetHourlyCounters()
    {
        self::query()->update(['messages_sent_this_hour' => 0]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVenomBot($query)
    {
        return $query->where('type', 'venom_bot');
    }

    public function markAsBanned()
    {
        $this->update([
            'status' => 'banned',
            'last_ban_date' => now(),
            'ban_count' => $this->ban_count + 1
        ]);
    }

    public function reactivate()
    {
        $this->update(['status' => 'active']);
    }
}
