<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Who extends Model
{
    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];
    public function whoItems()
    {
        return $this->hasMany(WhoItems::class);
    }

    public function whoWorks()
    {
        return $this->hasMany(WhoWorks::class);
    }
}
