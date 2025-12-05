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
        'bankable',
        'createdBy'
    ];

    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function bankable()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'banks_id', 'id');
    }
}
