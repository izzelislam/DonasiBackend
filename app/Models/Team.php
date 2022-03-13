<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uuid',
    ];

    // scope where has user status active
    public function scopeActive($query)
    {
        $query->whereHas('users', function ($query) {
            $query->where('status', 'active');
        });
    }
    
    public function donors()
    {
        return $this->hasMany(Donor::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
