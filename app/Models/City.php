<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasTranslations;

    protected $guarded = ['created_at', 'updated_at'];

    protected $translatable = ['name'];

    public static $relatio = [
        'users',
    ];

    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'city_id');
    }
}
