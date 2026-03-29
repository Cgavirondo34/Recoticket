<?php

namespace App\Services\Gym;

use App\Models\GymPayment;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\PaymentMethod;

class PaymentService
{
    /**
     * Register a payment for a member's membership.
     */
    public function registerPayment(array $data): GymPayment
    {
        $payment = GymPayment::create([
            'tenant_id'            => $data['tenant_id'] ?? null,
            'member_id'            => $data['member_id'],
            'member_membership_id' => $data['member_membership_id'] ?? null,
            'payment_method_id'    => $data['payment_method_id'] ?? null,
            'amount'               => $data['amount'],
            'paid_at'              => $data['paid_at'],
            'status'               => 'paid',
            'reference'            => $data['reference'] ?? null,
            'notes'                => $data['notes'] ?? null,
        ]);

        return $payment;
    }

    /**
     * Mark a payment as paid (e.g. after Mercado Pago webhook confirmation).
     */
    public function confirmPayment(GymPayment $payment, string $mercadoPagoId): GymPayment
    {
        $payment->update([
            'status'          => 'paid',
            'mercadopago_id'  => $mercadoPagoId,
            'paid_at'         => now()->toDateString(),
        ]);

        return $payment;
    }

    /**
     * Generate a Mercado Pago preference for a payment.
     * Abstraction layer — actual API call delegated to MercadoPagoService.
     */
    public function createMercadoPagoPreference(GymPayment $payment, MercadoPagoService $mpService): array
    {
        return $mpService->createPreference([
            'title'       => 'Membresía — ' . $payment->member->full_name,
            'quantity'    => 1,
            'unit_price'  => (float) $payment->amount,
            'external_reference' => 'gym_payment_' . $payment->id,
        ]);
    }
}
