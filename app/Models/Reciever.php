<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reciever extends Model
{
    use HasFactory, HasTranslations;
    public static $permissions = [
        'recievers.view',
    ];
    protected $translatable = ['city_name', 'state_name', 'country_name'];

    public static $relatio = [
        'user',
    ];


    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }
    protected $guarded = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
