<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect('/admin/');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:8'],
        ]);

        // Additional security: Check if user exists and is admin before attempting login
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !$user->is_admin) {
            return back()->withErrors([
                'email' => 'Invalid credentials or insufficient permissions.',
            ])->withInput($request->only('email'));
        }

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log successful admin login
            \Illuminate\Support\Facades\Log::info('Admin login successful', [
                'user_id' => auth()->id(),
                'email' => auth()->user()->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended('/admin/');
        }

        // Log failed login attempt
        \Illuminate\Support\Facades\Log::warning('Failed admin login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}
