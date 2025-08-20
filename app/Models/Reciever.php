<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reciever extends Model
{
    use HasFactory;
    public static $permissions = [
        'recievers.view',
        'recievers.create',
        'recievers.update',
        'recievers.delete',
    ];

    public static $relatio = [
        'city',
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

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
