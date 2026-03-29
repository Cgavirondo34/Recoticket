<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'member_id', 'member_membership_id', 'payment_method_id',
        'amount', 'status', 'type', 'reference', 'mp_payment_id', 'mp_status',
        'paid_at', 'registered_by', 'notes', 'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
        'metadata' => 'array',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function membership() { return $this->belongsTo(MemberMembership::class, 'member_membership_id'); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class); }
    public function registeredBy() { return $this->belongsTo(User::class, 'registered_by'); }

    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
}
