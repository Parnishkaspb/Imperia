<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return match (Auth::user()->role_id) {
                1 => redirect()->intended('/admin'),
                2 => redirect()->intended('/manufacture'),
                3 => redirect()->intended('/manufacture'),
                4 => redirect()->intended('/manufacture'),
                5 => redirect()->intended('/manufacture'),
                default => redirect()->intended('/login'),
            };
        }

        return back()->withInput()->withErrors([
            'email' => 'Неверные логин или пароль',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Вы успешно вышли из системы!');
    }
}
