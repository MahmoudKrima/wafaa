<?php

namespace App\Models;

use App\Enum\ActivationStatusEnum;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory, HasTranslations;

    protected $guarded = ['created_at', 'updated_at'];

    public $translatable = ['title', 'description'];

    public static $permissions = [
        'services.view',
        'services.create',
        'services.update',
        'services.delete'
    ];

    protected $casts = [
        'status' => ActivationStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::created(function () {
            Cache::forget('services');
        });

        static::updated(function () {
            Cache::forget('services');
        });

        static::deleted(function () {
            Cache::forget('services');
        });
    }
}
