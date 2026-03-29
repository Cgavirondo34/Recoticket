<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\FieldTimeSlot;
use App\Models\MembershipPlan;
use App\Models\PaymentMethod;
use App\Models\WhatsappTemplate;
use Illuminate\Database\Seeder;

class GymSeeder extends Seeder
{
    public function run(): void
    {
        // ── Payment Methods ────────────────────────────────────────────────────
        $methods = [
            ['name' => 'Efectivo',       'code' => 'cash'],
            ['name' => 'Transferencia',  'code' => 'transfer'],
            ['name' => 'Mercado Pago',   'code' => 'mercadopago'],
            ['name' => 'Tarjeta débito', 'code' => 'debit_card'],
            ['name' => 'Tarjeta crédito','code' => 'credit_card'],
        ];
        foreach ($methods as $m) {
            PaymentMethod::firstOrCreate(['code' => $m['code']], $m + ['active' => true]);
        }

        // ── Membership Plans ───────────────────────────────────────────────────
        $plans = [
            ['name' => 'Plan mensual', 'price' => 8000, 'duration_days' => 30, 'description' => 'Acceso completo por 30 días'],
            ['name' => 'Plan trimestral', 'price' => 20000, 'duration_days' => 90, 'description' => 'Acceso por 3 meses'],
            ['name' => 'Plan anual', 'price' => 70000, 'duration_days' => 365, 'description' => 'Acceso por 1 año'],
        ];
        foreach ($plans as $p) {
            MembershipPlan::firstOrCreate(['name' => $p['name']], $p + ['active' => true]);
        }

        // ── Field Time Slots ────────────────────────────────────────────────────
        $slots = [
            ['label' => 'Turno mañana 1',    'starts_at' => '08:00', 'ends_at' => '09:00', 'price' => 3000],
            ['label' => 'Turno mañana 2',    'starts_at' => '09:00', 'ends_at' => '10:00', 'price' => 3000],
            ['label' => 'Turno mediodía',    'starts_at' => '12:00', 'ends_at' => '13:00', 'price' => 3000],
            ['label' => 'Turno tarde 1',     'starts_at' => '17:00', 'ends_at' => '18:00', 'price' => 4000],
            ['label' => 'Turno tarde 2',     'starts_at' => '18:00', 'ends_at' => '19:00', 'price' => 4000],
            ['label' => 'Turno tarde 3',     'starts_at' => '19:00', 'ends_at' => '20:00', 'price' => 4000],
            ['label' => 'Turno noche 1',     'starts_at' => '20:00', 'ends_at' => '21:00', 'price' => 3500],
            ['label' => 'Turno noche 2',     'starts_at' => '21:00', 'ends_at' => '22:00', 'price' => 3500],
        ];
        foreach ($slots as $s) {
            FieldTimeSlot::firstOrCreate(['label' => $s['label']], $s + ['active' => true]);
        }

        // ── Expense Categories ──────────────────────────────────────────────────
        $categories = [
            ['name' => 'Alquiler',         'color' => '#ef4444'],
            ['name' => 'Servicios',        'color' => '#f97316'],
            ['name' => 'Equipamiento',     'color' => '#3b82f6'],
            ['name' => 'Mantenimiento',    'color' => '#8b5cf6'],
            ['name' => 'Marketing',        'color' => '#ec4899'],
            ['name' => 'Sueldos',          'color' => '#10b981'],
            ['name' => 'Limpieza',         'color' => '#6b7280'],
            ['name' => 'Otros',            'color' => '#94a3b8'],
        ];
        foreach ($categories as $c) {
            ExpenseCategory::firstOrCreate(['name' => $c['name']], $c);
        }

        // ── WhatsApp Templates ──────────────────────────────────────────────────
        $templates = [
            [
                'event' => 'payment_due_soon',
                'name'  => 'Pago próximo a vencer',
                'body'  => "Hola {{nombre}} 👋\n\nTe recordamos que tu membresía *{{plan}}* vence el {{fecha}}.\n\nPodés renovarla en el gimnasio o por transferencia. ¡Cualquier consulta estamos disponibles! 💪",
            ],
            [
                'event' => 'payment_due_today',
                'name'  => 'Pago vence hoy',
                'body'  => "Hola {{nombre}} ⚠️\n\nTu membresía *{{plan}}* vence HOY.\n\nAcercate al gimnasio para renovarla y seguir entrenando sin interrupciones. 🏋️",
            ],
            [
                'event' => 'payment_overdue',
                'name'  => 'Pago vencido',
                'body'  => "Hola {{nombre}} 📋\n\nTu membresía venció el {{fecha}}.\n\nPara continuar accediendo al gimnasio, por favor renovate lo antes posible. ¡Te esperamos! 💪",
            ],
            [
                'event' => 'payment_confirmed',
                'name'  => 'Pago confirmado',
                'body'  => "Hola {{nombre}} ✅\n\nRecibimos tu pago de *{{monto}}*.\n\n¡Gracias! Tu membresía está activa. ¡A entrenar! 🏋️💪",
            ],
            [
                'event' => 'reservation_confirmed',
                'name'  => 'Reserva confirmada',
                'body'  => "Hola {{nombre}} 🏟️\n\n¡Tu reserva está confirmada!\n\n📅 Fecha: {{fecha}}\n⏰ Turno: {{turno}}\n\nCualquier cambio avisanos con anticipación. ¡Nos vemos!",
            ],
            [
                'event' => 'reservation_reminder',
                'name'  => 'Recordatorio de reserva',
                'body'  => "Hola {{nombre}} ⏰\n\nTe recordamos que tenés una cancha reservada mañana:\n\n📅 {{fecha}}\n⏰ {{turno}}\n\n¡Hasta mañana! ⚽",
            ],
        ];
        foreach ($templates as $t) {
            WhatsappTemplate::firstOrCreate(['event' => $t['event']], $t + ['active' => true]);
        }
    }
}
