<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Service extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public static $permissions = [
        'services.view',
        'services.update',
    ];

    protected static function booted(): void
    {
        static::updated(function () {
            Cache::forget('services');
        });
    }

    public function items()
    {
        return $this->hasMany(ServiceItem::class, 'service_id', 'id');
    }
}
