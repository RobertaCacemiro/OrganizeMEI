<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Charge;
use App\Models\Client;

class ChargeController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        // Lista de clientes separados
        $clients = Client::where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Mapa de status
        $statusMap = [
            1 => 'Pendente',
            2 => 'Pago',
            3 => 'Vencido',
        ];

        // Cobranças com cliente
        $charges = Charge::with('client')
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->orderBy('due_date', 'desc')
            ->paginate(10)
            ->through(function ($charge) use ($statusMap) {
                return [
                    'id' => $charge->id,
                    'client_id' => $charge->client_id,
                    'client_name' => $charge->client ? $charge->client->name : null,
                    'data_vencimento' => Carbon::parse($charge->due_date)->format('d/m/Y'),
                    'data_pagamento' => $charge->payment_date ? Carbon::parse($charge->payment_date)->format('d/m/Y') : null,
                    'valor' => $charge->amount,
                    'status_codigo' => $charge->status,
                    'status' => $statusMap[$charge->status] ?? 'Desconhecido',
                ];
            });

        return Inertia::render('Cobrancas', [
            'data' => $charges,
            'clients' => $clients,
        ]);
    }

    public function destroy($id)
    {
        $charge = Charge::findOrFail($id);

        $userId = auth()->id();
        $meiId = session('mei_id');

        if ($charge->mei_id !== $meiId) {
            abort(403, 'Acesso negado: cobrança não pertence ao seu MEI.');
        }

        if ($charge->user_id !== $userId) {
            abort(403, 'Acesso negado: você não cadastrou esse cobrança.');
        }

        $charge->delete();

        return Inertia::location(route('cobrancas.index'));

    }


}
