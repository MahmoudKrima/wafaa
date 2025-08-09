<?php

namespace App\Models;

use App\Models\Insight;
use App\Enums\InsightTypeEnum;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsightItem extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    protected $casts = [
        'type' => InsightTypeEnum::class,
    ];

    public function insight()
    {
        return $this->belongsTo(Insight::class);
    }

    protected static function booted(): void
    {
        static::updated(function () {
            Cache::forget('insights');
        });
    }
}
