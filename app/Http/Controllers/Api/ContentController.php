<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Traits\ResponseFormater;
use Exception;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    use ResponseFormater;

    public function index()
    {
        $data = Content::orderBy('created_at','desc')->paginate(5);
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

    public function search()
    {
        try {
            $keyword = request('q');

            if (isset($keyword)){
                $data = Content::where('title', 'LIKE', '%'.$keyword.'%')->orderBy('created_at','desc')->get();
                return $this->successResponse($data);
            }else{
                return $this->errorResponse('Keyword not found', 404);
            }
        } catch (Exception $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
