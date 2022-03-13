<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function index()
    {
        return view('auth.login');
    }

    public function process(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|exists:users,phone_number',
            'password' => 'required',
        ],[
            'phone_number.required' => 'Nomor telepon tidak boleh kosong',
            'phone_number.exists' => 'Nomor telepon tidak terdaftar',
            'password.required' => 'Password tidak boleh kosong',
        ]);

        $credentials = $request->only('phone_number', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            // check role
            if (Auth::user()->role == 'admin') {
                return redirect()->route('dashboard');
            } else {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses');
            }
        }else{
            return redirect()->back()->with('error', 'Nomor telepon atau password salah');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
