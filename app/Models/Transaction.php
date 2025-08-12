<?php

namespace App\Models;

use App\Enum\TransactionStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = ['created_at', 'updated_at'];


    public static $permissions = [
        'transactions.view',
        'transactions.create',
        'transactions.update',
        'transactions.delete'
    ];

    protected $casts = [
        'status' => TransactionStatusEnum::class
    ];


    public static $relatio = [
        'user',
        'bank',
        'acceptedBy'
    ];

    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function acceptedBy()
    {
        return $this->belongsTo(Admin::class, 'accepted_by', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(Banks::class, 'banks_id', 'id');
    }
}
