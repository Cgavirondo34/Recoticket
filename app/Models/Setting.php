<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['tenant_id', 'key', 'value'];

    public function tenant() { return $this->belongsTo(Tenant::class); }

    /**
     * Get a setting value for a given tenant (or global if tenant_id is null).
     */
    public static function getValue(string $key, mixed $default = null, ?int $tenantId = null): mixed
    {
        $setting = static::where('key', $key)->where('tenant_id', $tenantId)->first();
        return $setting ? $setting->value : $default;
    }
}
