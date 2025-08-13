<?php

namespace App\Models;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slider extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];
    public static $permissions = [
        'sliders.view',
        'sliders.create',
        'sliders.update',
        'sliders.delete',
    ];
    protected $casts = [
        'status' => ActivationStatusEnum::class,
    ];
}
