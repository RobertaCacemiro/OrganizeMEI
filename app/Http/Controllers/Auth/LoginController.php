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

            $user = Auth::user();
            $mei = $user->meiProfile()->first();

            session([
                'id' => $user->id,
                'type' => $user->type,
                'access' => $user->access_permission,
                'mei_id' => $mei?->id,
            ]);

            if (empty($mei)) {
                return redirect()->route('profile-mei.index');
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas estão incorretas.',
        ])->onlyInput('email');


    }
}
