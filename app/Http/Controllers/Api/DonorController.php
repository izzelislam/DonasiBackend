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
                $data = Donor::where('team_id', $team_id)->orderBy('created_at','desc')->orderBy("created_at", "desc")->paginate(20);
                return $this->successResponse($data);
            }

            return $this->errorResponse('Team ID not found', 404);
        } catch (Exception $err) {
            return $this->errorResponse('something error', 500, $err);
        }
    }

    public function show($id)
    {
        try {
            $donor_uuid = request('donor_uuid');

            $check = Auth::user()->team_id;

            if (isset($check)){
                $data = Donor::where('team_id', $check)->where('uuid', $id)
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
                'province_id.required' => 'Provinsi wajib diisi',
                'regency_id.required'  => 'kabupaten wajib diisi',
                'district_id.required' => 'kecamaatan wajib diisi',
                'name.required'        => 'nama wajib diisi',
                'phone_number.required'=> 'nomor telepon wajib diisi',
                'address.required'     => 'alamat wajib diisi',
            ]);
    
            
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            if (($request->lat || $request->lng) == null){
                return $this->errorResponse('latitude dan longitude wajib diisi', 422);
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

    public function update(Request $request, $id)
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
                'province_id.required' => 'Provinsi wajib diisi',
                'regency_id.required'  => 'kabupaten wajib diisi',
                'district_id.required' => 'kecamaatan wajib diisi',
                'name.required'        => 'nama wajib diisi',
                'phone_number.required'=> 'nomor telepon wajib diisi',
                'address.required'     => 'alamat wajib diisi',
            ]);
    
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $donor = Donor::find($id)->update($request->all());
            
            return $this->successResponse($donor, 'Donor has been updated');

        } catch (\Throwable $err) {
           return $this->errorResponse('something error', 500, $err);
        }
    }

    public function destroy($id)
    {
        try {

            if (isset($id)){
                $donor = Donor::where('id', $id)->first();

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
                        ->get();

                $filtered_data = $data->where('team_id', Auth::user()->team_id);

                return $this->successResponse($filtered_data);
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
