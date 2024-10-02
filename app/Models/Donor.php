<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id',
        'province_id',
        'regency_id',
        'district_id',
        'uuid',
        'qr',
        'name',
        'phone_number',
        'address',
        'lat',
        'lng',
        'status',
    ];

    protected $appends = ['qr_url'];
    // protected $appends = ['qr_url', 'full_address', 'province', 'regency', 'district'];

    public function scopeForRegency($query)
    {
        if (isset(request()->regency_id)) {
            $query->where('regency_id', request()->regency_id);
        }
    }

    public function scopeForDistrict($query)
    {
        if (isset(request()->district_id)) {
            $query->where('district_id', request()->district_id);
        }
    }

    public function scopeForTeam($query)
    {
        if(isset(request()->team_id)) {
            $query->where('team_id', request()->team_id);
        }
    }

    public function scopeForProvince($query)
    {
        if (isset(request()->province_id)) {
            $query->where('province_id', request()->province_id);
        }
    }

    public function getQrUrlAttribute()
    {
        if($this->qr){
            return url($this->qr);
        }
    }

    public function team()
    {
        return $this->belongsTo(Team::class)->withTrashed();
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->district->name}, {$this->regency->name}, {$this->province->name}";
    }

    
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

}
