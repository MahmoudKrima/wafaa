<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SenderCity extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function sender()
    {
        return $this->belongsTo(Sender::class);
    }
}
