<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Traits\ResponseFormater;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    use ResponseFormater;

    public function index()
    {
        $data = Content::all();
        return $this->successResponse($data);
    }

    public function show()
    {
        $id = request('id');

        if (isset($id)){
            $data = Content::findOrFail($id);
            return $this->successResponse($data);
        }else{
            return $this->errorResponse('Content UUID not found', 404);
        }
    }
}
