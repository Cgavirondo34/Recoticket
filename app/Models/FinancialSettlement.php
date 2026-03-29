<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'year', 'month',
        'gym_income', 'field_income', 'total_income',
        'gym_expenses', 'field_expenses', 'shared_expenses', 'total_expenses',
        'gym_net', 'field_net', 'total_net',
        'partner_earnings', 'adjustments', 'status',
        'closed_by', 'closed_at', 'notes',
    ];

    protected $casts = [
        'gym_income' => 'decimal:2',
        'field_income' => 'decimal:2',
        'total_income' => 'decimal:2',
        'gym_expenses' => 'decimal:2',
        'field_expenses' => 'decimal:2',
        'shared_expenses' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'gym_net' => 'decimal:2',
        'field_net' => 'decimal:2',
        'total_net' => 'decimal:2',
        'partner_earnings' => 'array',
        'adjustments' => 'array',
        'closed_at' => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function closedBy() { return $this->belongsTo(User::class, 'closed_by'); }

    public function isClosed(): bool { return $this->status === 'closed'; }
    public function isDraft(): bool { return $this->status === 'draft'; }
}
