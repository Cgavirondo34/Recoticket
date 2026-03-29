<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'address',
        'timezone', 'currency', 'logo_path', 'active', 'settings',
    ];

    protected $casts = [
        'active' => 'boolean',
        'settings' => 'array',
    ];

    public function users() { return $this->hasMany(User::class); }
    public function members() { return $this->hasMany(Member::class); }
    public function membershipPlans() { return $this->hasMany(MembershipPlan::class); }
    public function fieldSlots() { return $this->hasMany(FieldSlot::class); }
    public function expenses() { return $this->hasMany(Expense::class); }
    public function financialSettlements() { return $this->hasMany(FinancialSettlement::class); }
    public function businessPartners() { return $this->hasMany(BusinessPartner::class); }
    public function notificationTemplates() { return $this->hasMany(NotificationTemplate::class); }
}
