<?php

namespace App\Models;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    /** @use HasFactory<\Database\Factories\TestimonialFactory> */
    use HasFactory, HasTranslations;

    public $translatable = ['name', 'job_title', 'review'];

    protected $guarded = ['created_at', 'updated_at'];

    public static $permissions = [
        'testimonials.view',
        'testimonials.create',
        'testimonials.update',
        'testimonials.delete'
    ];

    protected $casts = [
        'status' => ActivationStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::created(function () {
            Cache::forget('testimonials');
        });

        static::updated(function () {
            Cache::forget('testimonials');
        });

        static::deleted(function () {
            Cache::forget('testimonials');
        });
    }
}
