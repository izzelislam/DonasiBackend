<?php

namespace App\Traits;


trait ResponseFormater
{
    public function successResponse( $data, $message = 'success', $code = 200)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function errorResponse( $message, $code = 404, $data = null, )
    {
        return response()->json([
          'code' => $code,
          'error' => $message, 
          'data' => $data
        ], $code);
    }
}