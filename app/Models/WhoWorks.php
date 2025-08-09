<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhoWorks extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function who()
    {
        return $this->belongsTo(Who::class);
    }
}
