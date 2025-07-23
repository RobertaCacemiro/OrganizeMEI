<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credenciais)) {
            $request->session()->regenerate();
            session(['id' => Auth::id(), 'type' => Auth::user()->type]);
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas estão incorretas.',
        ])->onlyInput('email');
    }
}
