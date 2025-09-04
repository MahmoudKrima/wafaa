<?php

namespace App\Models;

use App\Enum\TransactionTypeEnum;
use App\Enum\WalletLogTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class WalletLog extends Model
{
    use HasTranslations;
    protected $guarded = ['created_at', 'updated_at'];
    protected $translatable = ['description'];
    public static $permissions = [
        'wallet_logs.view',
        'wallet_logs.create',
        'wallet_logs.update',
        'wallet_logs.delete'
    ];

    protected $casts = [
        'trans_type' => WalletLogTypeEnum::class,
        'type' => TransactionTypeEnum::class
    ];


    public static $relatio = [
        'user',
        'admin'
    ];

    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
