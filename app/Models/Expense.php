<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'expense_category_id', 'payment_method_id', 'description',
        'amount', 'business_unit', 'expense_date', 'receipt_path',
        'registered_by', 'notes', 'is_adjustment', 'adjusted_by', 'adjustment_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'is_adjustment' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function category() { return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class); }
    public function registeredBy() { return $this->belongsTo(User::class, 'registered_by'); }
    public function adjustedBy() { return $this->belongsTo(User::class, 'adjusted_by'); }
}
