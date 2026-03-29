<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementItem extends Model
{
    protected $fillable = [
        'financial_settlement_id', 'type', 'label', 'amount',
    ];

    protected $casts = ['amount' => 'decimal:2'];

    public function settlement() { return $this->belongsTo(FinancialSettlement::class, 'financial_settlement_id'); }
}
