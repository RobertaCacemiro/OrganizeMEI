<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    public function store(Request $request)
    {


//         $teste = session('id');
// showArray(["teste" => $teste]);
//  dd($teste);
// exit;


        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // if (Auth::attempt($data)) {
        //     $request->session()->regenerate();
        //     session(['id' => Auth::id(), 'type' => Auth::user()->type]);
        //     return redirect()->route('home');
        // }


    }
}
