<?php

namespace App\Traits;


trait ResponseFormater
{
    public function successResponse( $data, $message = 'success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function errorResponse( $message, $code = 404, $data = null, )
    {
        return response()->json([
          'status' => false,
          'error' => $message, 
          'data' => $data
        ], $code);
    }
}