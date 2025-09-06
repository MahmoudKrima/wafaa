<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enum\ActivationStatusEnum;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasTranslations;

    protected $guarded = ['created_at', 'updated_at'];

    public static $permissions = [
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $translatable = ['name', 'city_name', 'state_name', 'country_name'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => ActivationStatusEnum::class
        ];
    }

    public static $relatio = [
        'createdByAdmin',
        'addedByAdmin',
        'wallet',
        'transactions',
        'walletLogs',
        'shippingPrices',
        'notifications',
        'reciverable'
    ];


    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->wallet()->create();
        });
    }

    public function scopeWithAllRelations($query)
    {
        return $query->with(self::$relatio);
    }


    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function addedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'added_by');
    }

    public function wallet()
    {
        return $this->hasOne(UserWallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function walletLogs()
    {
        return $this->hasMany(WalletLog::class);
    }

    public function shippingPrices()
    {
        return $this->hasMany(UserShippingPrice::class);
    }

    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')
            ->latest();
    }
    public function reciverable()
    {
        return $this->morphMany(DatabaseNotification::class, 'reciverable')->latest();
    }
}
