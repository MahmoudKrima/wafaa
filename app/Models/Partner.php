<?php

namespace App\Models;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Partner extends Model
{
    /** @use HasFactory<\Database\Factories\PartnerFactory> */
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    protected $casts = [
        'status' => ActivationStatusEnum::class,
    ];

    public static $permissions = [
        'partners.view',
        'partners.create',
        'partners.update',
        'partners.delete'
    ];

    protected static function booted(): void
    {
        static::created(function () {
            Cache::forget('partners');
        });

        static::updated(function () {
            Cache::forget('partners');
        });

        static::deleted(function () {
            Cache::forget("partners");
        });
    }
}
