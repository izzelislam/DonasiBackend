<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Traits\ResponseFormater;
use Exception;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    use ResponseFormater;

    public function getProvince()
    {
        try {
            $data = Province::all();
            return $this->successResponse($data);
        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function getProvinceRegencies()
    {
        try {
            $data = Regency::where('province_id', request('province_id'))->get();
            return $this->successResponse($data);
        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function getRegencyDistricts()
    {
        try {
            $data = District::where('regency_id', request('regency_id'))->get();
            return $this->successResponse($data);
        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }

    public function getAll()
    {
      try {
          $data = Province::with('regencies.districts')->get();
            return $this->successResponse($data);
      } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
      }  
    }
}
