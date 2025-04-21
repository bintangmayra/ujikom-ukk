<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Cek role user untuk redirect
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->intended('/produk'); // admin ke halaman CRUD produk
            } elseif ($user->role === 'petugas') {
                return redirect()->intended('/produk'); // petugas juga ke produk (tapi nanti readonly)
            }

            return redirect('/'); // fallback
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
