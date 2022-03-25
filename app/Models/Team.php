<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

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
        return $this->hasMany(Donor::class)->withTrashed();
    }

    public function users()
    {
        return $this->hasMany(User::class)->withTrashed();
    }
}
