<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tech extends Model
{
    use HasFactory;


    protected $guarded = ['created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany(TechItem::class, 'tech_id', 'id');
    }
}
