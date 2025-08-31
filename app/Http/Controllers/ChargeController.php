<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Charge;
use App\Models\Client;
use App\Models\Payment;

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
            ->get(['id', 'name'])
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                ];
            });

        // dd([
        //     'userId' => $userId,
        //     'meiId' => $meiId,
        //     'clients' => $clients->all(),
        // ]);
        // exit;
        // Mapa de status
        $statusMap = [
            1 => 'Pendente Pagamento',
            2 => 'Pago',
            3 => 'Vencido'
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

        $dashboardValues = $this->getDashboardValues();

        // showArray(["clientes" => $clients]);exit;

        return Inertia::render('Cobrancas', [
            'data' => $charges,
            'clients' => $clients,
            'dashboardValues' => $dashboardValues,
        ]);
    }

    public function getDashboardValues()
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        $query = Charge::where('user_id', $userId)
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
            'total_pagos'     => $countPagos,
            'total_pendentes' => $countPendentes,
            'total_atrasados' => $countAtrasados,
        ];
    }

    public function store(Request $request)
    {
        $request->merge([
            'ies_send_pix' => filter_var($request->ies_send_pix, FILTER_VALIDATE_BOOLEAN)
        ]);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'ies_send_pix' => 'required|bool',
            // 'status' => 'required|in:1,2,3',
            'description' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
        ]);

        $charge = Charge::create([
            'user_id' => auth()->id(),
            'mei_id' => session('mei_id'),
            'client_id' => $validated['client_id'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'],
            'payment_date' => $validated['payment_date'],
            'status' => $validated['status'] ?? 1,
        ]);

        $access_permission = session('access');

        if ($validated['ies_send_pix'] && in_array($access_permission, [1, 2]) && empty($validated['payment_date'])) {
            // echo "<br>TESTE";
            // echo "<br>Envia PIX";
            // echo "<br>Acesso permitido";
            // echo ""


            // exit;
            Payment::create(attributes: [
                'user_id' => auth()->id(),
                'mei_id' => session('mei_id'),
                'client_id' => $validated['client_id'],
                'charge_id' => $charge->id,
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
                'payment_date' => null,
                'status' => 1,
                'sent' => 0,
            ]);
        } else if ($validated['ies_send_pix'] && !in_array($access_permission, [1, 2])) {
            return back()->withErrors([
                'ies_send_pix' => 'Você não tem permissão para gerar PIX.',
            ]);
        }

        return Inertia::location(route('cobrancas.index'));
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
