<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'expense_category_id', 'payment_method_id',
        'description', 'amount', 'expense_date', 'business_unit', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function category() { return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class); }

    public function scopeGym($query) { return $query->where('business_unit', 'gym'); }
    public function scopeField($query) { return $query->where('business_unit', 'field'); }
    public function scopeShared($query) { return $query->where('business_unit', 'shared'); }
    public function scopeForMonth($query, int $year, int $month) {
        return $query->whereYear('expense_date', $year)->whereMonth('expense_date', $month);
    }
}
