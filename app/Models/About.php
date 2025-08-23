<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class About extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];
    public static $permissions = [
        'about.view',
        'about.update',
    ];

}
