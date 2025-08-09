<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechItem extends Model
{
    use HasFactory;


    protected $guarded = ['created_at', 'updated_at'];

    public function tech()
    {
        return $this->belongsTo(Tech::class, 'tech_id', 'id');
    }
}
