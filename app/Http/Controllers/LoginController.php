<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function get()
    {
        return view('content.login');
    }

    public function post(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::whereNull('google_id')->where('email', $validated['email'])->first();
        if ($user) {
            if (Hash::check($validated['password'], $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();

                return redirect()->route('home');
            }
        }

        return back()->withErrors([
            'email' => 'Sai tài khoản hoặc mật khẩu.',
        ]);
    }
}
