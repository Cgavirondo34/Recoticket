<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'member_id', 'member_membership_id', 'payment_method_id',
        'amount', 'paid_at', 'status', 'reference', 'mercadopago_id', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function member() { return $this->belongsTo(Member::class); }
    public function membership() { return $this->belongsTo(MemberMembership::class, 'member_membership_id'); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class); }

    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopePaid($query) { return $query->where('status', 'paid'); }
    public function scopeOverdue($query) { return $query->where('status', 'overdue'); }
}
