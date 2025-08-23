<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminSetting extends Model
{
    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];

    public static $permissions = [
        'admin_settings.update',
    ];


    public static $relatio = [
        'admin',
    ];

    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
