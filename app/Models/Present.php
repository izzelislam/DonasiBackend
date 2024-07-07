<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Present extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'present_at',
        'type',
        'note',
        'lat',
        'long',
    ];

    // public function setPresentAtAttribute()
    // {
    //     $this->attributes['present_at'] = now();
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
