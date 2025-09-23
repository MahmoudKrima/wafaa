<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sender extends Model
{
    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];
    public static $permissions = [
        'senders.view',
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
