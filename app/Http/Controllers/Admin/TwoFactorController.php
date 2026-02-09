<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function show()
    {
        return view('admin.2fa.setup');
    }

    public function enable(Request $request)
    {
        $user = $request->user();
        // Minimal enrollment: mark two_factor_enabled true
        $user->two_factor_enabled = true;
        $user->save();

        return redirect()->route('admin.2fa.setup')->with('success', 'Two-factor authentication enabled for your account.');
    }

    public function disable(Request $request)
    {
        $user = $request->user();
        $user->two_factor_enabled = false;
        $user->save();

        return redirect()->route('admin.2fa.setup')->with('success', 'Two-factor authentication disabled for your account.');
    }
}
