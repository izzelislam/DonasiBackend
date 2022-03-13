<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'receipt_uid',
        'recipient',
        'type',
        'amount',
        'note',
    ];

    public function scopeMonth($query)
    {
        if (isset(request()->month)){
            $query->whereMonth('created_at', request()->month);
        }else{
            $query->whereMonth('created_at', date('m'));
        }
    }

    // scope start_date
    public function scopeStartDate($query)
    {
        if (isset(request()->start_date)){
            $query->where('created_at', '>=', request()->start_date);
        }
    }

    public function scopeEndDate($query)
    {
        if (isset(request()->end_date)){
            $query->where('created_at', '<=', request()->end_date);
        }
    }

    public function scopeYear($query)
    {
        if (isset(request()->year)){
            $query->whereYear('created_at', request()->year);
        }else{
            $query->whereYear('created_at', date('Y'));
        }
    }

    public function scopeRange($query)
    {
        if (isset(request()->start_date) && isset(request()->end_date)){
            $query->whereBetween('created_at', [request()->start_date, request()->end_date]);
        }
    }

    public function scopeTeam($query)
    {
        if (isset(request()->team_id)){
            $query->whereHas('donor', function ($query) {
                $query->where('team_id', request()->team_id);
            });
        }
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
