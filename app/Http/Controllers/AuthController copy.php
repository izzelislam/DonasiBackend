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
        // Support field named 'login', 'email', or 'phone_number'
        $loginValue = $request->input('login') ?? $request->input('email') ?? $request->input('phone_number');

        if (empty($loginValue)) {
            return redirect()->back()->withErrors(['login' => 'Email atau nomor telepon wajib diisi'])->withInput();
        }

        $request->validate([
            'password' => 'required',
        ], [
            'password.required' => 'Password tidak boleh kosong',
        ]);

        $loginInput = trim($loginValue);
        $password = $request->input('password');

        // Check if input is an email or phone number
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $request->has('remember'))) {
            // check role
            if (Auth::user()->role == 'admin') {
                return redirect()->route('dashboard');
            } else {
                $this->logout();
                return redirect()->back()->with('error', 'Anda tidak memiliki akses');
            }
        } else {
            return redirect()->back()->with('error', 'Email/Nomor telepon atau password salah')->withInput(['login' => $loginInput]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
