<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Term extends Model
{

    use HasTranslations;

    protected $guarded = ['created_at', 'updated_at'];
    protected $translatable = ['term_description', 'policy_description'];

    public static $permissions = [
        'terms.view',
        'terms.update',
    ];
}
