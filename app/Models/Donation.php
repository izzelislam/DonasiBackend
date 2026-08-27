<?php

namespace App\Models;

use App\Enums\BankAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'receipt_uid',
        'recipient',
        'bank_account',
        'bank_name',
        'account_number',
        'account_name',
        'type',
        'amount',
        'note',
        'proof_image',
        'created_at',
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

    public function getBankAccountEnumAttribute(): ?BankAccount
    {
        return $this->bank_account ? BankAccount::tryFrom($this->bank_account) : null;
    }

    public function getRecipientAccountDisplayAttribute(): string
    {
        if ($this->account_number) {
            $bank = $this->bank_name ? "{$this->bank_name} " : '';
            return "{$bank}{$this->account_number}";
        }

        if ($this->bank_account && $enum = BankAccount::tryFrom($this->bank_account)) {
            return $enum->formattedReceipt();
        }

        return '-';
    }

    public function getProofImageBase64Attribute()
    {
        if ($this->proof_image && file_exists(public_path($this->proof_image))) {
            $path = public_path($this->proof_image);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }
}
