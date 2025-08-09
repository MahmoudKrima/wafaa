<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IndustryItem extends Model
{
    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];

    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id', 'id');
    }
}
