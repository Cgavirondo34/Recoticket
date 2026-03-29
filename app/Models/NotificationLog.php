<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'notification_template_id', 'event_key', 'channel',
        'recipient_phone', 'recipient_name', 'member_id', 'rendered_body',
        'status', 'provider', 'provider_message_id', 'attempts',
        'sent_at', 'error_message', 'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function template() { return $this->belongsTo(NotificationTemplate::class, 'notification_template_id'); }
    public function member() { return $this->belongsTo(Member::class); }
}
