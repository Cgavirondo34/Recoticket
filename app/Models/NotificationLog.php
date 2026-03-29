<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'tenant_id', 'channel', 'recipient', 'event',
        'body', 'status', 'error', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
