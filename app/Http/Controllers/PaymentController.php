<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

use App\Models\Client;

class PaymentController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        // Lista de clientes separados
        $clients = Client::where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                ];
            });

        $statusMap = [
            1 => 'Pendente Envio',
            2 => 'Pendente Pagamento',
            3 => 'Pago',
            4 => 'Atrasado',
        ];

        $payments = Payment::with(['client', 'charge'])
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($payments) use ($statusMap) {
                return [
                    'id' => $payments->id,
                    'charge_id' => $payments->charge_id,
                    'cliente_id' => $payments->client_id,
                    'cliente_name' => $payments->client ? $payments->client->name : null,
                    'data_vencimento' => $payments->due_date ? Carbon::parse($payments->due_date)->format('d/m/Y') : null,
                    'data_pagamento' => $payments->return_at ? Carbon::parse($payments->return_at)->format('d/m/Y') : null,
                    'data_envio' => $payments->sent_at ? Carbon::parse($payments->sent_at)->format('d/m/Y') : null,
                    'valor' => $payments->amount,
                    'status_codigo' => $payments->status,
                    'status' => $statusMap[$payments->status] ?? 'Desconhecido',
                ];
            });

        return Inertia::render('Pagamentos', [
            'data' => $payments
        ]);
    }

    public function getDashboardValues()
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        $query = Payment::where('user_id', $userId)
            ->where('mei_id', $meiId);

        // Total em dinheiro
        $totalPendentes = (clone $query)->where('status', 1)->sum('amount');
        $totalPagos = (clone $query)->where('status', 2)->sum('amount');
        $totalAtrasados = (clone $query)->where('status', 3)->sum('amount');

        // Quantidade de registros
        $countPendentes = (clone $query)->where('status', 1)->count();
        $countPagos = (clone $query)->where('status', 2)->count();
        $countAtrasados = (clone $query)->where('status', 3)->count();

        // Calcular total em dinheiro
        $totalCobrancas = ($countPendentes + $countPagos + $countAtrasados);


        return [
            'total_cobrancas' => $totalCobrancas,
            'total_pagos' => $countPagos,
            'total_pendentes' => $countPendentes,
            'total_atrasados' => $countAtrasados,
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'charge_id' => 'nullable|exists:charges,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'payment_date' => 'nullable|date',
            'status' => 'required|in:1,2,3',
        ]);

        Payment::create(attributes: [
            'user_id' => auth()->id(),
            'mei_id' => session('mei_id'),
            'client_id' => $validated['client_id'],
            'charge_id' => $validated['charge_id'] ?? null,
            'amount' => $validated['amount'],
            'due_date' => $validated['due_date'],
            'payment_date' => $validated['payment_date'] ?? null,
            'status' => $validated['status'],
            'sent' => 0,
        ]);

        return redirect()->route('payments.index');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return back();
    }
}
