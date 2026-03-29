<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialSettlement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'year', 'month', 'gym_income', 'field_income',
        'total_income', 'total_expenses', 'net_income',
        'partner_distributions', 'status', 'notes',
    ];

    protected $casts = [
        'gym_income' => 'decimal:2',
        'field_income' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_income' => 'decimal:2',
        'partner_distributions' => 'array',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function items() { return $this->hasMany(SettlementItem::class); }
}
