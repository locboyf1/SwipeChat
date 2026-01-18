<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function get()
    {
        return view('content.register');
    }

    public function post(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::whereNull('google_id')->where('email', $validated['email'])->first();
        if ($user) {
            return back()->withErrors([
                'email' => 'Email đã tồn tại.',
            ]);
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('login.get');
    }
}
