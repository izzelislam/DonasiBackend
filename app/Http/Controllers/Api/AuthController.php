<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseFormater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use App\Models\User;
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
            $user = User::with('team')->where('id', Auth::user()->id)->first();
            return $this->successResponse($user);
        } catch (Exception $err) {
            return $this->errorResponse('something error', 500, $err);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'required',
                'phone_number' => 'required',
                'email' => 'required|email',
                'profile_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'password' => 'min:4|max:20',
            ],[
                'name.required' => 'Nama wajib di isi',
                'phone_number.required' => 'Nomor telepon wajib di isi',
                'email.required' => 'Email wajib di isi',
                'email.email' => 'Email tidak valid',
                'profile_image.image' => 'Format gambar tidak valid',
                'profile_image.mimes' => 'Format gambar tidak valid',
                'profile_image.max' => 'Ukuran gambar terlalu besar',
                'password.min' => 'Password minimal 4 karakter',
                'password.max' => 'Password maksimal 20 karakter',
            ]);
                
           

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $user = User::find(Auth::user()->id);

            if ($request->file('profile_image')) {
                $this->deleteFile($user->image);
                $image = $request->file('profile_image');
                $request['image'] = $this->uploadFile($image);
            }else{
                $request['image'] = $user->profile_image;
            }

            if ($request->password){
                $request['password'] = bcrypt($request->password);
            }else{
                $request['password'] = $user->password;
            }

            $user->update($request->all());

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

    public function deleteFile($name)
    {
        if (file_exists($name)){
            unlink($name);
        }
    }

    public function uploadFile($file)
    {
        $newName = uniqid().'.'.$file->getClientOriginalExtension();
        $file->move('images/profile/', $newName);
        return 'images/profile/'.$newName;
    }
}
