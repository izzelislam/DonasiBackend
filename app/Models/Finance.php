<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'receipt_uid',
        'type',
        'amount',
        'note',
    ];

    public function scopeType($query)
    {
        if (isset(request()->type)){
            $query->where('type', request()->type);
        }
    }

    public function scopeRange($query)
    {
        if (isset(request()->start_date) && isset(request()->end_date)){
            $query->whereBetween('created_at', [request()->start_date, request()->end_date]);
        }

        // start_date
        if (isset(request()->start_date)){
            $query->whereDate('created_at', '>=', request()->start_date);
        }

        // end_date
        if (isset(request()->end_date)){
            $query->whereDate('created_at', '<=', request()->end_date);
        }
    }

    public function scopeMonth($query)
    {
        if (isset(request()->month)){
            $query->whereMonth('created_at', request()->month);
        }else{
            $query->whereMonth('created_at', date('m'));
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

    // get data only this month
    public function scopeThisMonth($query)
    {
        $query->whereMonth('created_at', date('m'));
    }
    
   
}
