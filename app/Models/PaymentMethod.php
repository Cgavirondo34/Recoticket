<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'name', 'code', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
}
