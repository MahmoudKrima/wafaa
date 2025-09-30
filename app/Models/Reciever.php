<?php

namespace App\Models;

use App\Models\RecieverCity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reciever extends Model
{
    use HasFactory;
    public static $permissions = [
        'recievers.view',
    ];

    public static $relatio = [
        'user',
        'shippingCompanies',
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

    public function shippingCompanies()
    {
        return $this->hasMany(RecieverCity::class, 'reciever_id', 'id');
    }
}
