<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'event_key', 'name', 'body',
        'available_variables', 'active', 'channel',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'active' => 'boolean',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function logs() { return $this->hasMany(NotificationLog::class); }

    /** Render body by replacing {{variable}} placeholders */
    public function render(array $variables): string
    {
        $body = $this->body;
        foreach ($variables as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        return $body;
    }
}
