<?php

namespace App\Http\Controllers\Api;

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
            $data = Donation::with('donor')->month()->year()->team()->range()->startDate()->endDate()->get();
            return $this->successResponse($data);
        } catch (Exception $err) {
            return $this->errorResponse('something error', 500, $err);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'uuid' => 'required|exists:donors,uuid',
                'amount'   => 'required',
                'type'   => 'required',
            ],[
                'uuid.required' => 'uuid is required',
                'uuid.exists' => 'uuid not found',
                'amount.required'   => 'amount is required',
                'type.required'   => 'type is required',
            ]);

            
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $request['receipt_uid'] = 'INV-'.uniqid().date('dmY');
            $request['recipient'] = Auth::user()->id;
            $request['donor_id']     = Donor::where('uuid', $request->uuid)->first()->id;
    
            $donation = Donation::create($request->all());
            return $this->successResponse($donation);

        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'id' => 'required|exists:donations,id',
                'uuid' => 'required|exists:donors,uuid',
                'amount'   => 'required',
                'type'   => 'required',
            ],[
                'id.required' => 'id is required',
                'id.exists' => 'id not found',
                'uuid.required' => 'uuid is required',
                'uuid.exists' => 'uuid not found',
                'amount.required'   => 'amount is required',
                'type.required'   => 'type is required',
            ]);


            if($validator->fails()){
                return $this->errorResponse($validator->errors(), 422);
            }

            $request['recipient'] = Auth::user()->id;
            $request['donor_id']     = Donor::where('uuid', $request->uuid)->first()->id;

            $donation = Donation::findOrFail($request->id);
            $donation->update($request->all());
            return $this->successResponse($donation);

        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function destroy() 
    {
        try {
            $donation = Donation::findOrFail(request('id'));
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
            $data = Donation::with('donor')->month()->year()->sum('amount');
            return $this->successResponse($data);

        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    
}
