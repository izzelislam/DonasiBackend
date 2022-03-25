<?php

namespace App\Http\Controllers\Api;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\ResponseFormater;
use Illuminate\Http\Request;
use App\Models\Donor;
use Exception;

class DonorController extends Controller
{
    use ResponseFormater;

    public function index()
    {
        try {

            $team_id = Auth::user()->team_id;

            if (isset($team_id)){
                $data = Donor::where('team_id', $team_id)->orderBy('created_at','desc')->paginate(20);
                return $this->successResponse($data);
            }

            return $this->errorResponse('Team ID not found', 404);
        } catch (Exception $err) {
            return $this->errorResponse('something error', 500, $err);
        }
    }

    public function show()
    {
        try {
            $donor_uuid = request('donor_uuid');

            $check = Auth::user()->team_id;

            if (isset($check)){
                $data = Donor::where('team_id', $check)->where('uuid', $donor_uuid)
                        ->with(['team', 'province', 'regency','district', 'donations' => function($query){
                            $query->year();
                        }])
                        ->withSum('donations','amount')
                        ->first();
                if (isset($data)){
                    return $this->successResponse($data);
                }
                return $this->errorResponse('Donatur Tidak ditemukan', 404);
            }

        } catch (Exception $err) {
            return $this->errorResponse('something error', 500, $err);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'province_id' => 'required',
                'regency_id'  => 'required',
                'district_id' => 'required',
                'name'        => 'required',
                'phone_number'=> 'required',
                'address'     => 'required',
            ], [
                'province_id.required' => 'Province ID is required',
                'regency_id.required'  => 'Regency ID is required',
                'district_id.required' => 'District ID is required',
                'name.required'        => 'Name is required',
                'phone_number.required'=> 'Phone Number is required',
                'address.required'     => 'Address is required',
            ]);
    
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }
    
            $request['team_id'] = Auth::user()->team_id;

            if (empty($request['team_id'])){
                return $this->errorResponse('Team ID not found', 404);
            }

            $request['uuid']   = 'DNR-' . uniqid();
            $request['status'] = 'active';
            $request['qr']     = $this->generateQrCode($request['uuid']);
    
            $donor = Donor::create($request->all());
    
            return $this->successResponse($donor, 'Donor has been created');
        } catch (Exception $err) {
            return $this->errorResponse('something error', 500, $err);
        }

    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'id'          => 'required|exists:donors,id',
                'province_id' => 'required',
                'regency_id'  => 'required',
                'district_id' => 'required',
                'name'        => 'required',
                'phone_number'=> 'required',
                'address'     => 'required',
            ], [
                'id.required'          => 'Donor ID is required',
                'id.exists'            => 'Donor ID not found',
                'province_id.required' => 'Province ID is required',
                'regency_id.required'  => 'Regency ID is required',
                'district_id.required' => 'District ID is required',
                'name.required'        => 'Name is required',
                'phone_number.required'=> 'Phone Number is required',
                'address.required'     => 'Address is required',
            ]);
    
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $donor = Donor::find($request['id'])->update($request->all());
            
            return $this->successResponse($donor, 'Donor has been updated');

        } catch (\Throwable $err) {
           return $this->errorResponse('something error', 500, $err);
        }
    }

    public function destroy()
    {
        try {
            $donor_uuid = request('donor_uuid');

            if (isset($donor_uuid)){
                $donor = Donor::where('uuid', $donor_uuid)->first();

                if (empty($donor)){
                    return $this->errorResponse('Donor not found', 404);
                }

                $donor->delete();

                return $this->successResponse($donor, 'Donor has been deleted');
            }else{
                return $this->errorResponse('Donor UUID not found', 404);
            }
       
        } catch (\Throwable $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function search()
    {
        try {
            $q = request('q');

            if (isset($q)){
                $data = Donor::where('name', 'like', '%' . $q . '%')
                        ->orWhere('phone_number', 'like', '%' . $q . '%')
                        ->orWhere('address', 'like', '%' . $q . '%')
                        ->orWhere('uuid', 'like', '%' . $q . '%')
                        ->where('team_id', Auth::user()->team_id)
                        ->get();

                return $this->successResponse($data);
            }else{
                return $this->errorResponse('keyword tidak ditemukan', 404);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function generateQrCode($param) 
    {
        QrCode::size(500)
                ->errorCorrection('H')
                ->format('svg')
                ->style('round')
                ->generate($param, public_path('/qr/'.$param.'.svg'));

        return 'qr/'.$param.'.svg';
    }
}
