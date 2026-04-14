<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $credentials = [
            Str::contains($request->login, '@') ? 'email' : 'name' => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors(['login' => 'Credenciales incorrectas.'])
            ->onlyInput('login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
