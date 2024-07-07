<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'permit_at',
        'note'
    ];

    // public function setPermitAtAttribute()
    // {
    //     $this->attributes['permit_at'] = now();
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
