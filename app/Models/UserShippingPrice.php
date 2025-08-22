<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class UserShippingPrice extends Model
{
    use HasTranslations;
    protected $translatable = ['company_name'];

    public static $permissions = [
        'user_shipping_prices.view',
        'user_shipping_prices.create',
        'user_shipping_prices.update',
        'user_shipping_prices.delete',
    ];

    protected $guarded = ['created_at', 'updated_at'];

    public static $relatio = [
        'user',
    ];


    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
