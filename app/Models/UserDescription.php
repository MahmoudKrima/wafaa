<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserDescription extends Model
{
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
