<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class ServiceItem extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public static $permissions = [
        'service_items.view',
        'service_items.update',
    ];

    protected static function booted(): void
    {
        static::updated(function () {
            Cache::forget('services');
        });
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}
