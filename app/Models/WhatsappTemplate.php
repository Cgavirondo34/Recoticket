<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $fillable = [
        'tenant_id', 'event', 'name', 'body', 'active',
    ];

    protected $casts = ['active' => 'boolean'];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    // Available template events
    public static function events(): array
    {
        return [
            'payment_due_soon'      => 'Pago próximo a vencer (3 días)',
            'payment_due_today'     => 'Pago vence hoy',
            'payment_overdue'       => 'Pago vencido',
            'payment_confirmed'     => 'Pago confirmado',
            'reservation_confirmed' => 'Reserva confirmada',
            'reservation_reminder'  => 'Recordatorio de reserva',
        ];
    }

    /**
     * Render the template with given variables.
     */
    public function render(array $vars = []): string
    {
        $body = $this->body;
        foreach ($vars as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        return $body;
    }
}
