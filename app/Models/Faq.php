<?php

namespace App\Models;

use App\Enum\ActivationStatusEnum;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public static $permissions = [
        'faqs.view',
        'faqs.create',
        'faqs.update',
        'faqs.delete',
    ];

    protected $casts = [
        'status' => ActivationStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::created(function () {
            Cache::forget('faqs');
        });

        static::updated(function () {
            Cache::forget('faqs');
        });

        static::deleted(function () {
            Cache::forget('faqs');
        });
    }
}
