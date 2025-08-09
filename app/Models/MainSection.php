<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainSection extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany(MainSectionItems::class, 'main_section_id', 'id');
    }
}
