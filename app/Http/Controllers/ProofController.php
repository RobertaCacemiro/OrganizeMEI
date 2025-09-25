<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProofController extends Controller
{
    public function show($key)
    {
        $payment = Payment::where('key', $key)->firstOrFail();

        return Inertia::render('Comprovante', [
            'payment' => [
                'id' => $payment->id,
                'key' => $payment->key,
                'valor' => $payment->amount,
                'descricao' => optional($payment->charge)->description,
                'cliente' => optional($payment->client)->name,
                'cidade' => optional($payment->mei)->city,
            ]
        ]);
    }
}
