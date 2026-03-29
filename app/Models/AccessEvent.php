<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'member_id', 'access_token_id', 'token_used',
        'direction', 'result', 'denial_reason', 'device_id',
        'verified_by', 'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function accessToken() { return $this->belongsTo(AccessToken::class); }
    public function verifiedBy() { return $this->belongsTo(User::class, 'verified_by'); }

    public function wasGranted(): bool { return $this->result === 'granted'; }
}
