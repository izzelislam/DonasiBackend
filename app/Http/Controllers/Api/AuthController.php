<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseFormater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use Exception;

class AuthController extends Controller
{
    use ResponseFormater;

    public function login(Request $request)
    {
       try {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|exists:users,phone_number',
            'password' => 'required',
        ],[
            'phone_number.required' => 'Nomor telepon wajib di isi',
            'phone_number.exists' => 'Nomor telepon tidak terdaftar',
            'password.required' => 'Password wajib di isi',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $credentials = $request->only('phone_number', 'password');

        $isLogin = Auth::attempt($credentials);

        if (!$isLogin){
            return $this->errorResponse('Password atau nomor telepon salah', 402);
        }

        if (Auth::user()->role == 'admin'){
            return $this->errorResponse('tidak memeliki akses', 403);
        }

        $user = Auth::user();

        $data = [
            'token' => $user->createToken('auth-token')->plainTextToken,
            'type' => 'Bearer',
            'user' => $user
        ];

        return $this->successResponse($data);
       } catch (Exception $err) {
           return $this->errorResponse('something error', 500, $err);
       }
    }

    
    public function me()
    {
        try {
            $user = Auth::user();

            return $this->successResponse($user);
        } catch (Exception $err) {
            return $this->errorResponse('something error', 500, $err);
        }
    }


    public function logout()
    {
        try {
            $user = Auth::user();
            $user->tokens()->delete();
            return $this->successResponse(null, 'Logout success');
        } catch (Exception $err) {
            return $this->errorResponse('something error', 500, $err);
        }
    }

    public function settings()
    {
        try {
            $data = Setting::first();
            return $this->successResponse($data);
        } catch (Exception $th) {
            return $this->errorResponse('something error', 500, $th);
        }
    }
}
