<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    //

    protected $guarded = ['created_at', 'updated_at'];
    public static $permissions = [
        'user_wallet.view',
        'user_wallet.create',
        'user_wallet.update',
        'user_wallet.delete',
    ];

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
