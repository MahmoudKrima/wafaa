<?php

namespace App\Models;

use App\Enum\ContactStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public static $permissions = [
        'contacts.view',
        'contacts.reply',
        'contacts.delete',
    ];

    protected $casts = [
        'status' => ContactStatusEnum::class,
    ];
}
