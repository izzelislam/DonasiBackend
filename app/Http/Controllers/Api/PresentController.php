<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Present;
use App\Traits\ResponseFormater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PresentController extends Controller
{
    use ResponseFormater;
    public function index(Request $request)
    {
        $moonth = $request->moonth;
        if (!$moonth) {
            $moonth = date('m');
        }
        

        $data = Present::query();
        $data = $data->whereMonth('present_at', $moonth)->whereYear('present_at', date('Y'))->orderBy("present_at", "desc")->where("user_id", auth()->user()->id);

        if ($request->type){
            $data = $data->where("type", $request->type);
        }

        return $this->successResponse($data->get());
    }

    public function show($id)
    {
        $data = Present::find($id);
        if (!$data) {
            return $this->errorResponse('data not found', 404);
        }
        return $this->successResponse($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'type'          => 'required',
            'note'          => 'nullable|max:500',
            'lat'           => 'required',
            'long'          => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $is_presented = Present::where('user_id', $request->user_id)
        ->where("type", $request->type)->where("present_at", ">", now()->subDays(1))
        ->first(); 

        if ($is_presented) {
            return $this->errorResponse('sudah pernah melakukan present', 402);
        }

        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        $data['present_at'] = now();
        $res = Present::create($data);
        return $this->successResponse($res);
    }

    public function destroy($id)
    {
        $model = Present::find($id);
        if (!$model) {
            return $this->errorResponse('data not found', 404);
        }

        $model->delete();
        return $this->successResponse($model);
    }
}
