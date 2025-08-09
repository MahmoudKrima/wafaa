<?php

namespace App\Models;

use App\Models\InsightItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Insight extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function insightItems()
    {
        return $this->hasMany(InsightItem::class);
    }

    protected static function booted(): void
    {
        static::updated(function () {
            Cache::forget('insights');
        });
    }
}
