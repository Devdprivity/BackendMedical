<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'type',
        'is_active',
        'config',
        'consultation_fee',
        'currency',
        'instructions',
        'order',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'consultation_fee' => 'decimal:2',
    ];

    // Tipos de pago disponibles
    const TYPES = [
        'paypal' => 'PayPal',
        'binance_pay' => 'Binance Pay',
        'pago_movil' => 'Pago Móvil',
        'stripe' => 'Stripe',
        'wepay' => 'WePay',
    ];

    // Monedas soportadas
    const CURRENCIES = [
        'USD' => 'Dólar Estadounidense',
        'EUR' => 'Euro',
        'VES' => 'Bolívar Venezolano',
        'USDT' => 'Tether USD',
        'BTC' => 'Bitcoin',
        'BNB' => 'Binance Coin',
    ];

    /**
     * Relación con el usuario (doctor/admin) que configuró el método
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la clínica
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Relación con los pagos realizados con este método
     */
    public function payments()
    {
        return $this->hasMany(AppointmentPayment::class);
    }

    /**
     * Scope para métodos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para un tipo específico
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Obtener el nombre legible del tipo
     */
    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Obtener el nombre de la moneda
     */
    public function getCurrencyNameAttribute()
    {
        return self::CURRENCIES[$this->currency] ?? $this->currency;
    }

    /**
     * Verificar si es un método de pago manual (requiere verificación)
     */
    public function isManualPayment()
    {
        return in_array($this->type, ['pago_movil', 'binance_pay']);
    }

    /**
     * Verificar si es un método de pago automático
     */
    public function isAutomaticPayment()
    {
        return in_array($this->type, ['paypal', 'stripe', 'wepay']);
    }

    /**
     * Obtener configuración específica por clave
     */
    public function getConfig($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Generar URL de pago para métodos automáticos
     */
    public function generatePaymentUrl($appointment, $amount = null)
    {
        $amount = $amount ?? $this->consultation_fee;
        
        switch ($this->type) {
            case 'paypal':
                return $this->generatePayPalUrl($appointment, $amount);
            case 'stripe':
                return $this->generateStripeUrl($appointment, $amount);
            default:
                return null;
        }
    }

    /**
     * Generar URL de PayPal
     */
    private function generatePayPalUrl($appointment, $amount)
    {
        $params = [
            'cmd' => '_xclick',
            'business' => $this->getConfig('paypal_email'),
            'item_name' => "Consulta Médica - Dr. {$appointment->doctor->name}",
            'item_number' => $appointment->id,
            'amount' => $amount,
            'currency_code' => $this->currency,
            'return' => route('payments.success', ['payment' => 'PAYMENT_ID']),
            'cancel_return' => route('payments.cancel', ['appointment' => $appointment->id]),
            'notify_url' => route('payments.webhook.paypal'),
            'custom' => $appointment->id,
        ];

        return 'https://www.paypal.com/cgi-bin/webscr?' . http_build_query($params);
    }

    /**
     * Validar configuración del método de pago
     */
    public function validateConfig()
    {
        switch ($this->type) {
            case 'paypal':
                return !empty($this->getConfig('paypal_email')) && 
                       filter_var($this->getConfig('paypal_email'), FILTER_VALIDATE_EMAIL);
            
            case 'binance_pay':
                return !empty($this->getConfig('binance_id'));
            
            case 'pago_movil':
                return !empty($this->getConfig('receiver_phone')) && 
                       !empty($this->getConfig('receiver_cedula')) && 
                       !empty($this->getConfig('receiver_bank'));
            
            default:
                return true;
        }
    }

    /**
     * Obtener instrucciones formateadas para el paciente
     */
    public function getFormattedInstructions($appointment = null, $amount = null)
    {
        $amount = $amount ?? $this->consultation_fee;
        $instructions = $this->instructions;

        // Reemplazar variables dinámicas
        if ($appointment) {
            $instructions = str_replace([
                '{doctor_name}',
                '{appointment_date}',
                '{amount}',
                '{currency}',
                '{appointment_id}',
            ], [
                $appointment->doctor->name ?? 'Doctor',
                $appointment->date_time->format('d/m/Y H:i'),
                number_format($amount, 2),
                $this->currency,
                $appointment->id,
            ], $instructions);
        }

        return $instructions;
    }
}
