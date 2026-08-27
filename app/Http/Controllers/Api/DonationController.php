<?php

namespace App\Http\Controllers\Api;

use App\Enums\BankAccount;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Donor;
use App\Traits\ResponseFormater;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    use ResponseFormater;

    public function index()
    {
        try {
            $data = Donation::with('donor')->month()->year()->team()->range()->startDate()->endDate()->orderBy('created_at', 'desc')
                    ->whereHas('donor', function($query){
                        $query->where('team_id', Auth::user()->team_id);
                    })
                    ->paginate(20);
            return $this->successResponse($data);
        } catch (Exception $err) {
            return $this->errorResponse('something error', 500, $err);
        }
    }

    public function show($id)
    {
        try {
            $cek = Donation::with('donor')->where('id', $id)->first();

            if (empty($cek)) {
                return $this->errorResponse('donasi tidak ditemukan', 404);
            }

            if($cek->donor->team_id != Auth::user()->team_id){
                return $this->errorResponse('kode tidak valid', 404);
            }

            return $this->successResponse($cek);

        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'uuid'   => 'required|exists:donors,uuid',
                'amount' => 'required',
                'type'   => 'required',
            ],[
                'uuid.required'   => 'uuid wajib di isi',
                'uuid.exists'     => 'uuid tidak terdaftar',
                'amount.required' => 'amount wajib di isi',
                'type.required'   => 'type wajib di isi',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $payload = $request->all();
            $payload['receipt_uid'] = 'INV-'.uniqid().date('dmY');
            $payload['recipient']   = Auth::user()->name;
            $payload['donor_id']    = Donor::where('uuid', $request->uuid)->first()->id;

            if ($request->filled('bank_account')) {
                $enum = BankAccount::tryFrom($request->bank_account);
                if ($enum && $enum !== BankAccount::LAINNYA) {
                    $payload['bank_account']   = $enum->value;
                    $payload['bank_name']      = $enum->bankName();
                    $payload['account_number'] = $enum->accountNumber();
                    $payload['account_name']   = $enum->accountName();
                } elseif ($request->bank_account === BankAccount::LAINNYA->value) {
                    $payload['bank_account']   = BankAccount::LAINNYA->value;
                    $payload['bank_name']      = $request->custom_bank_name ?: 'Bank BSI';
                    $payload['account_number'] = $request->custom_account_number ?: '-';
                    $payload['account_name']   = $request->custom_account_name ?: '-';
                }
            }
    
            $donation = Donation::create($payload)->load('donor');
            return $this->successResponse($donation);

        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(),[
                'uuid'   => 'required|exists:donors,uuid',
                'amount' => 'required',
                'type'   => 'required',
            ],[
                'uuid.required'   => 'uuid wajib di isi',
                'uuid.exists'     => 'uuid tidak terdaftar',
                'amount.required' => 'amount wajib di isi',
                'type.required'   => 'type wajib di isi',
            ]);

            if($validator->fails()){
                return $this->errorResponse($validator->errors(), 422);
            }

            $payload = $request->all();
            $payload['recipient'] = Auth::user()->name;
            $payload['donor_id']  = Donor::where('uuid', $request->uuid)->first()->id;

            if ($request->filled('bank_account')) {
                $enum = BankAccount::tryFrom($request->bank_account);
                if ($enum && $enum !== BankAccount::LAINNYA) {
                    $payload['bank_account']   = $enum->value;
                    $payload['bank_name']      = $enum->bankName();
                    $payload['account_number'] = $enum->accountNumber();
                    $payload['account_name']   = $enum->accountName();
                } elseif ($request->bank_account === BankAccount::LAINNYA->value) {
                    $payload['bank_account']   = BankAccount::LAINNYA->value;
                    $payload['bank_name']      = $request->custom_bank_name ?: 'Bank BSI';
                    $payload['account_number'] = $request->custom_account_number ?: '-';
                    $payload['account_name']   = $request->custom_account_name ?: '-';
                }
            }

            $donation = Donation::findOrFail($id);
            $donation->update($payload);
            return $this->successResponse($donation);

        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function destroy($id) 
    {
        try {
            $donation = Donation::findOrFail($id);
            $donation->delete();

            return $this->successResponse(null, 'success delete');
        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function search()
    {
        try {
            $q = request('q');
            $data = Donation::with('donor')->where('receipt_uid', 'LIKE', '%'.$q.'%')->get();
            return $this->successResponse($data);
        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function getTotalDonation()
    {
        try {
            $data = Donation::with('donor')
                ->whereHas('donor', function ($query) {
                $query->where('team_id', Auth::user()->team_id);
            })
            ->month()->year()->sum('amount');
            return $this->successResponse($data);

        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }
}
