<?php

namespace App\Models;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Banks extends Model
{
    use HasTranslations;

    protected $guarded = ['created_at', 'updated_at'];

    public $translatable = ['name', 'account_owner'];

    public static $permissions = [
        'banks.view',
        'banks.create',
        'banks.update',
        'banks.delete'
    ];

    protected $casts = [
        'status' => ActivationStatusEnum::class
    ];
    public static $relatio = [
        'admin',
        'createdBy'
    ];

    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
