<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'source', 'event_type', 'external_id',
        'payload', 'signature', 'verified', 'processed_at', 'processing_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'verified' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    public function isProcessed(): bool { return $this->processed_at !== null; }
}
