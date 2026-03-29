<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'member_id', 'token', 'type', 'active', 'expires_at'];

    protected $casts = [
        'active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function accessEvents() { return $this->hasMany(AccessEvent::class); }

    public function isValid(): bool
    {
        return $this->active && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
