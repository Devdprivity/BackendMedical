<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'doctor_id',
        'patient_id',
        'payment_method_id',
        'amount',
        'currency',
        'concept',
        'description',
        'expires_at',
        'viewed_at',
        'is_active',
        'is_used',
        'payment_status',
        'appointment_payment_id',
        'paid_at',
        'metadata',
        'view_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'viewed_at' => 'datetime',
        'paid_at' => 'datetime',
        'is_active' => 'boolean',
        'is_used' => 'boolean',
        'metadata' => 'array',
    ];

    // Estados del link de pago
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';

    const STATUSES = [
        self::STATUS_PENDING => 'Pendiente',
        self::STATUS_COMPLETED => 'Completado',
        self::STATUS_FAILED => 'Fallido',
        self::STATUS_EXPIRED => 'Expirado',
    ];

    /**
     * Generar token único al crear
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paymentLink) {
            if (empty($paymentLink->token)) {
                $paymentLink->token = self::generateUniqueToken();
            }
        });
    }

    /**
     * Relación con el doctor
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Relación con el paciente
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relación con el método de pago
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Relación con el pago de cita (si existe)
     */
    public function appointmentPayment()
    {
        return $this->belongsTo(AppointmentPayment::class);
    }

    /**
     * Scope para links activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope para links no expirados
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope para links pendientes
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', self::STATUS_PENDING);
    }

    /**
     * Verificar si el link está expirado
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Verificar si el link está disponible para uso
     */
    public function isAvailable()
    {
        return $this->is_active && 
               !$this->is_used && 
               !$this->isExpired() && 
               $this->payment_status === self::STATUS_PENDING;
    }

    /**
     * Marcar como visto
     */
    public function markAsViewed($metadata = [])
    {
        $this->increment('view_count');
        
        if (!$this->viewed_at) {
            $this->update([
                'viewed_at' => now(),
                'metadata' => array_merge($this->metadata ?? [], $metadata)
            ]);
        }
    }

    /**
     * Marcar como usado
     */
    public function markAsUsed($appointmentPaymentId = null)
    {
        $this->update([
            'is_used' => true,
            'payment_status' => self::STATUS_COMPLETED,
            'paid_at' => now(),
            'appointment_payment_id' => $appointmentPaymentId,
        ]);
    }

    /**
     * Marcar como expirado
     */
    public function markAsExpired()
    {
        $this->update([
            'payment_status' => self::STATUS_EXPIRED,
            'is_active' => false,
        ]);
    }

    /**
     * Generar token único
     */
    public static function generateUniqueToken()
    {
        do {
            $token = Str::random(32);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Obtener la URL del link de pago
     */
    public function getPaymentUrl()
    {
        return route('payment-link.show', ['token' => $this->token]);
    }

    /**
     * Obtener la URL del código QR
     */
    public function getQrCodeUrl()
    {
        return route('payment-link.qr', ['token' => $this->token]);
    }

    /**
     * Generar código QR
     */
    public function generateQrCode($size = 200)
    {
        return QrCode::size($size)
                    ->format('png')
                    ->generate($this->getPaymentUrl());
    }

    /**
     * Obtener información pública del link (sin datos sensibles)
     */
    public function getPublicInfo()
    {
        return [
            'token' => $this->token,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'concept' => $this->concept,
            'description' => $this->description,
            'expires_at' => $this->expires_at->toISOString(),
            'is_expired' => $this->isExpired(),
            'is_available' => $this->isAvailable(),
            'doctor' => [
                'name' => $this->doctor->name,
                'specialty' => $this->doctor->specialty,
                'avatar' => $this->doctor->avatar,
            ],
            'patient' => $this->patient ? [
                'name' => $this->patient->name,
            ] : null,
            'payment_method' => [
                'type' => $this->paymentMethod->type,
                'type_name' => $this->paymentMethod->type_name,
                'is_manual' => $this->paymentMethod->isManualPayment(),
            ],
        ];
    }

    /**
     * Obtener configuración del método de pago (datos públicos necesarios)
     */
    public function getPaymentConfig()
    {
        $config = $this->paymentMethod->config;
        
        switch ($this->paymentMethod->type) {
            case 'pago_movil':
                return [
                    'receiver_phone' => $config['receiver_phone'] ?? '',
                    'receiver_bank' => $config['receiver_bank'] ?? '',
                    'receiver_name' => $config['receiver_name'] ?? '',
                    'receiver_cedula' => $config['receiver_cedula'] ?? '',
                ];
                
            case 'binance_pay':
                return [
                    'binance_id' => $config['binance_id'] ?? '',
                ];
                
            case 'paypal':
                return [
                    'paypal_email' => $config['paypal_email'] ?? '',
                ];
                
            default:
                return [];
        }
    }

    /**
     * Obtener el nombre del estado
     */
    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->payment_status] ?? $this->payment_status;
    }
} 