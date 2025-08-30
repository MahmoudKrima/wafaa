<?php

namespace App\Models;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Model;

class AllowedCompany extends Model
{
    public static $permissions = [
        'allowed_companies.view',
        'allowed_companies.update',
    ];
    protected $guarded = ['created_at', 'updated_at'];

    public static $relatio = [
        'admin',
    ];

    protected function casts(): array
    {
        return [
            'status' => ActivationStatusEnum::class
        ];
    }


    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
