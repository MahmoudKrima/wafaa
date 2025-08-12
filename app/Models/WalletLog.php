<?php

namespace App\Models;

use App\Enum\TransactionTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class WalletLog extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    public static $permissions = [
        'wallet_logs.view',
        'wallet_logs.create',
        'wallet_logs.update',
        'wallet_logs.delete'
    ];

    protected $casts = [
        'type' => TransactionTypeEnum::class
    ];


    public static $relatio = [
        'user',
        'loggable'
    ];
    public function loggable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
