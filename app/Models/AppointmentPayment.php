<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AppointmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'payment_method_id',
        'payment_reference',
        'status',
        'payment_type',
        'amount',
        'currency',
        'exchange_rate',
        'payment_data',
        'paid_at',
        'verified_at',
        'verified_by',
        'notes',
        'receipt_url',
    ];

    protected $casts = [
        'payment_data' => 'array',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // Estados del pago
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    const STATUSES = [
        self::STATUS_PENDING => 'Pendiente',
        self::STATUS_COMPLETED => 'Completado',
        self::STATUS_FAILED => 'Fallido',
        self::STATUS_CANCELLED => 'Cancelado',
        self::STATUS_REFUNDED => 'Reembolsado',
    ];

    /**
     * Generar referencia única al crear
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_reference)) {
                $payment->payment_reference = $payment->generateUniqueReference();
            }
        });
    }

    /**
     * Relación con la cita
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Relación con el paciente
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relación con el doctor
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Relación con el método de pago
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Relación con el usuario que verificó el pago
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope para pagos pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope para pagos completados
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope para pagos que requieren verificación
     */
    public function scopeRequiringVerification($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->whereHas('paymentMethod', function($q) {
                        $q->whereIn('type', ['pago_movil', 'binance_pay']);
                    });
    }

    /**
     * Obtener el nombre del estado
     */
    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Verificar si el pago está completado
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Verificar si el pago está pendiente
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Verificar si requiere verificación manual
     */
    public function requiresManualVerification()
    {
        return $this->paymentMethod && $this->paymentMethod->isManualPayment();
    }

    /**
     * Marcar como completado
     */
    public function markAsCompleted($verifiedBy = null, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
            'notes' => $notes,
            'paid_at' => $this->paid_at ?? now(),
        ]);

        // Actualizar el estado de la cita
        if ($this->appointment) {
            $this->appointment->update(['payment_status' => 'paid']);
        }
    }

    /**
     * Marcar como fallido
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'notes' => $reason,
        ]);

        // Actualizar el estado de la cita
        if ($this->appointment) {
            $this->appointment->update(['payment_status' => 'failed']);
        }
    }

    /**
     * Generar referencia única
     */
    private function generateUniqueReference()
    {
        do {
            $reference = strtoupper(Str::random(8));
        } while (self::where('payment_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Obtener datos específicos del pago
     */
    public function getPaymentData($key, $default = null)
    {
        return $this->payment_data[$key] ?? $default;
    }

    /**
     * Establecer datos específicos del pago
     */
    public function setPaymentData($key, $value)
    {
        $data = $this->payment_data ?? [];
        $data[$key] = $value;
        $this->payment_data = $data;
        $this->save();
    }

    /**
     * Generar URL para compartir información de pago
     */
    public function getPaymentUrl()
    {
        return route('payments.show', ['reference' => $this->payment_reference]);
    }

    /**
     * Generar QR code para el pago
     */
    public function generateQRCode()
    {
        $paymentUrl = $this->getPaymentUrl();
        
        // Usar una librería de QR code (por ejemplo, SimpleSoftwareIO/simple-qrcode)
        return \QrCode::size(200)
                     ->format('png')
                     ->generate($paymentUrl);
    }

    /**
     * Obtener instrucciones de pago formateadas
     */
    public function getFormattedInstructions()
    {
        if (!$this->paymentMethod) {
            return 'No hay instrucciones disponibles.';
        }

        return $this->paymentMethod->getFormattedInstructions($this->appointment, $this->amount);
    }

    /**
     * Validar datos de pago según el tipo
     */
    public function validatePaymentData($data)
    {
        switch ($this->payment_type) {
            case 'paypal':
                return isset($data['paypal_email']) && 
                       filter_var($data['paypal_email'], FILTER_VALIDATE_EMAIL);
            
            case 'binance_pay':
                return isset($data['binance_id']) && 
                       isset($data['network']);
            
            case 'pago_movil':
                return isset($data['sender_phone']) && 
                       isset($data['sender_cedula']) && 
                       isset($data['reference']) && 
                       strlen($data['reference']) === 6;
            
            default:
                return true;
        }
    }

    /**
     * Formatear monto con moneda
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }
}
