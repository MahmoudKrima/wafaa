<?php

namespace App\Models;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $guarded = ['created_at', 'updated_at'];

    public static $permissions = [
        'admins.view',
        'admins.create',
        'admins.update',
        'admins.delete',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status' => ActivationStatusEnum::class
    ];

    public static $relatio = [
        'createdUsers',
    ];

    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }

    /**
     * Get the users created by this admin.
     */
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }
}
