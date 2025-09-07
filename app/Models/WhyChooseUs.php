<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class WhyChooseUs extends Model
{
    //

    protected $guarded = ['created_at', 'updated_at'];

    public static $permissions = [
        'why_choose_us.update',
    ];


    protected static function booted(): void
    {
        static::updated(function () {
            Cache::forget('why_choose_us');
        });
    }
}
