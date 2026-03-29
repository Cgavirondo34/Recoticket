<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\NotificationTemplate;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Seed the default platform data for a new tenant.
     * Safe to run multiple times (upsert logic used).
     */
    public function run(): void
    {
        $this->seedPaymentMethods();
        $this->seedExpenseCategories();
        $this->seedNotificationTemplates();
    }

    private function seedPaymentMethods(): void
    {
        $methods = [
            ['name' => 'Efectivo',       'code' => 'cash'],
            ['name' => 'Transferencia',  'code' => 'transfer'],
            ['name' => 'Mercado Pago',   'code' => 'mercado_pago'],
            ['name' => 'Débito',         'code' => 'debit'],
            ['name' => 'Crédito',        'code' => 'credit'],
            ['name' => 'Manual / Otro',  'code' => 'manual'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(['code' => $method['code']], array_merge($method, ['active' => true]));
        }
    }

    private function seedExpenseCategories(): void
    {
        $categories = [
            ['name' => 'Alquiler',           'slug' => 'rent',           'business_unit' => 'shared',        'color' => '#6366f1'],
            ['name' => 'Servicios',          'slug' => 'utilities',      'business_unit' => 'shared',        'color' => '#8b5cf6'],
            ['name' => 'Sueldos',            'slug' => 'salaries',       'business_unit' => 'gym',           'color' => '#ec4899'],
            ['name' => 'Equipamiento Gym',   'slug' => 'gym-equipment',  'business_unit' => 'gym',           'color' => '#ef4444'],
            ['name' => 'Mantenimiento',      'slug' => 'maintenance',    'business_unit' => 'shared',        'color' => '#f97316'],
            ['name' => 'Publicidad',         'slug' => 'advertising',    'business_unit' => 'shared',        'color' => '#eab308'],
            ['name' => 'Insumos Cancha',     'slug' => 'field-supplies', 'business_unit' => 'football_field','color' => '#22c55e'],
            ['name' => 'Seguros',            'slug' => 'insurance',      'business_unit' => 'shared',        'color' => '#14b8a6'],
            ['name' => 'Impuestos',          'slug' => 'taxes',          'business_unit' => 'shared',        'color' => '#3b82f6'],
            ['name' => 'Otros',              'slug' => 'other',          'business_unit' => 'shared',        'color' => '#94a3b8'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['active' => true])
            );
        }
    }

    private function seedNotificationTemplates(): void
    {
        $templates = [
            [
                'event_key' => 'payment_due',
                'name'      => 'Membresía por vencer',
                'channel'   => 'whatsapp',
                'body'      => "Hola {{member_name}} 👋\n\nTu membresía *{{plan_name}}* vence el *{{expiry_date}}*.\n\n¿Querés renovarla? Escribinos o acercate al gimnasio. 🏋️",
                'available_variables' => ['member_name', 'plan_name', 'expiry_date'],
            ],
            [
                'event_key' => 'payment_overdue',
                'name'      => 'Membresía vencida',
                'channel'   => 'whatsapp',
                'body'      => "Hola {{member_name}} 👋\n\nTu membresía *{{plan_name}}* venció el *{{expiry_date}}*.\n\nPor favor regularizá tu situación para continuar entrenando. 💪",
                'available_variables' => ['member_name', 'plan_name', 'expiry_date'],
            ],
            [
                'event_key' => 'payment_confirmed',
                'name'      => 'Pago confirmado',
                'channel'   => 'whatsapp',
                'body'      => "✅ Hola {{member_name}}, tu pago de *{{amount}}* fue confirmado.\n\nTu membresía está activa hasta el *{{expiry_date}}*. ¡Gracias!",
                'available_variables' => ['member_name', 'amount', 'expiry_date'],
            ],
            [
                'event_key' => 'reservation_confirmed',
                'name'      => 'Reserva confirmada',
                'channel'   => 'whatsapp',
                'body'      => "⚽ Hola {{customer_name}}, tu reserva de cancha está confirmada!\n\n📅 *{{reservation_date}}*\n⏰ *{{start_time}} - {{end_time}}*\n\nTe esperamos!",
                'available_variables' => ['customer_name', 'reservation_date', 'start_time', 'end_time'],
            ],
            [
                'event_key' => 'reservation_reminder',
                'name'      => 'Recordatorio de reserva',
                'channel'   => 'whatsapp',
                'body'      => "⏰ Hola {{customer_name}}, recordatorio de tu reserva de hoy:\n\n🕐 *{{start_time}} - {{end_time}}*\n\n¡Nos vemos!",
                'available_variables' => ['customer_name', 'start_time', 'end_time'],
            ],
            [
                'event_key' => 'booking_cancelled',
                'name'      => 'Reserva cancelada',
                'channel'   => 'whatsapp',
                'body'      => "❌ Hola {{customer_name}}, tu reserva del *{{reservation_date}}* a las *{{start_time}}* fue cancelada.\n\nPodés contactarnos para reagendar.",
                'available_variables' => ['customer_name', 'reservation_date', 'start_time'],
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['event_key' => $template['event_key']],
                array_merge($template, [
                    'available_variables' => $template['available_variables'],
                    'active' => true,
                ])
            );
        }
    }
}
