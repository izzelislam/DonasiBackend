<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use App\Traits\ResponseFormater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermitController extends Controller
{
    use ResponseFormater;

    public function index()
    {
        $month = isset(request()->month) ? request()->month : date('m');
        $data = Permit::query();
        $data = $data->whereMonth('permit_at', $month)->whereYear('permit_at', date('Y'))->orderBy("permit_at", "desc")->where("user_id", auth()->user()->id);
        return $this->successResponse($data->get());
    }

    public function show($id)
    {
        $data = Permit::find($id);
        if (!$data) {
            return $this->errorResponse('data not found', 404);
        }
        return $this->successResponse($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'permit_at' => 'required',
            'note'      => 'nullable|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $is_exist_permit = Permit::where('user_id', auth()->user()->id)
        ->whereDate("permit_at", $request->permit_at)
        ->first();

        if ($is_exist_permit) {
            return $this->errorResponse('sudah mengajukan perizinan untuk hari ini', 402);
        }

        $request["user_id"] = auth()->user()->id;
        $request["permit_at"] = date("Y-m-d H:i:s", strtotime($request->permit_at));
        $data = $request->all();

        $res = Permit::create($data);
        return $this->successResponse($res);
    }

    public function destroy($id)
    {
        $data = Permit::find($id);
        if (!$data) {
            return $this->errorResponse('data not found', 404);
        }
        $data->delete();
        return $this->successResponse($data);
    }
}
