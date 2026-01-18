<?php

namespace App\Http\Controllers;

class LogoutController extends Controller
{
    public function post()
    {
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login.get');
    }
}
