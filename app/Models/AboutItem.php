<?php

namespace App\Models;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AboutItem extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];
    public static $permissions = [
        'about-items.view',
        'about-items.update',
    ];
   
}
