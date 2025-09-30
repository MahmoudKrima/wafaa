<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecieverCity extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function reciever()
    {
        return $this->belongsTo(Reciever::class);
    }
}
