<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                    'ies_envia_pix' => $charge->ies_send_pix,
                    'descricao' => $charge->description,
                    'status_codigo' => $charge->status,
                    'status' => $statusMap[$charge->status] ?? 'Desconhecido',
                ];
            });

        $dashboardValues = $this->getDashboardValues();

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
            'total_pagos' => $countPagos,
            'total_pendentes' => $countPendentes,
            'total_atrasados' => $countAtrasados,
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'client_id' => 'required|exists:clients,id',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
                'ies_send_pix' => 'required|boolean',
                'description' => 'nullable|string|max:255',
                'payment_date' => 'nullable|date',
            ],
            [
                'client_id.required' => 'Um cliente deve ser informado.',
                'amount.required' => 'Informe o valor da cobrança.',
                'due_date.required' => 'Informe a data de vencimento para a cobrança.',
                'ies_send_pix.required' => 'Informe se deseja enviar PIX.',
            ]
        );

        $userId = auth()->id();
        $meiId = session('mei_id');
        $access = session('access');

        try {
            DB::beginTransaction();

            // Cria a cobrança
            $charge = Charge::create([
                'user_id' => $userId,
                'mei_id' => $meiId,
                'client_id' => $validated['client_id'],
                'amount' => $validated['amount'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'],
                'payment_date' => $validated['payment_date'],
                'ies_send_pix' => $validated['ies_send_pix'],
                'status' => $validated['status'] ?? 1,
            ]);

            // Se for para enviar PIX
            if ($validated['ies_send_pix']) {
                if (!in_array($access, [1, 2])) {
                    DB::rollBack();
                    return back()->withErrors([
                        'ies_send_pix' => 'O envio de PIX com cobrança por e-mail está disponível apenas para clientes Premium.',
                    ]);
                }

                // Só cria Payment se ainda não tiver data de pagamento
                if (empty($validated['payment_date'])) {
                    Payment::create([
                        'user_id' => $userId,
                        'mei_id' => $meiId,
                        'client_id' => $validated['client_id'],
                        'charge_id' => $charge->id,
                        'amount' => $validated['amount'],
                        'due_date' => $validated['due_date'],
                    ]);
                }
            }

            DB::commit();

            return Inertia::location(route('cobrancas.index'));

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Erro ao criar cobrança', [
                'error' => $e->getMessage(),
                'user' => $userId,
                'mei' => $meiId,
            ]);

            return back()->withErrors([
                'general' => 'Não foi possível salvar a cobrança. Tente novamente mais tarde.',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $userId = auth()->id();
        $meiId = session('mei_id');
        $access = session('access');

        $charge = Charge::findOrFail($id);

        if ($charge->mei_id !== $meiId) {
            abort(403, 'Acesso negado: cobrança não pertence ao seu MEI.');
        }

        if ($charge->user_id !== $userId) {
            abort(403, 'Acesso negado: você não cadastrou essa cobrança.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'ies_send_pix' => 'required|boolean',
            'description' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Detecta mudança de false -> true
            $pixFoiAtivado = !$charge->ies_send_pix && $validated['ies_send_pix'];

            if ($pixFoiAtivado) {
                // Regra Premium
                if (!in_array($access, [1, 2])) {
                    DB::rollBack();
                    return back()->withErrors([
                        'ies_send_pix' => 'O envio de PIX está disponível apenas para clientes Premium.',
                    ]);
                }

                // Só se não houver pagamento registrado
                $paymentExists = Payment::where('charge_id', $charge->id)->exists();

                if ($paymentExists) {
                    DB::rollBack();
                    return back()->withErrors([
                        'ies_send_pix' => 'Já existe um PIX gerado para esta cobrança. Não é possível ativar novamente.',
                    ]);
                }

                // Só cria se ainda não tiver data de pagamento
                if (empty($validated['payment_date'])) {
                    Payment::create([
                        'user_id' => $userId,
                        'mei_id' => $meiId,
                        'client_id' => $validated['client_id'],
                        'charge_id' => $charge->id,
                        'amount' => $validated['amount'],
                        'due_date' => $validated['due_date'],
                    ]);
                }
            }

            // Atualiza os dados da cobrança
            $charge->update($validated);

            DB::commit();

            return Inertia::location(route('cobrancas.index'));
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Erro ao atualizar cobrança', [
                'error' => $e->getMessage(),
                'user' => $userId,
                'mei' => $meiId,
                'charge_id' => $id,
            ]);

            return back()->withErrors([
                'general' => 'Não foi possível atualizar a cobrança. Tente novamente mais tarde.',
            ]);
        }
    }

    public function destroy($id)
    {
        $charge = Charge::findOrFail($id);

        // Ajuste: buscar por charge_id (ou pelo campo correto do seu model)
        $payment = Payment::where('charge_id', $charge->id)->first();

        // Se houver pagamento atrelado com envio PIX, impedir exclusão
        if ($payment && $payment->status != 1 && !empty($payment->sent_at)) {
            return back()->withErrors([
                'general' => "Não foi possível excluir a cobrança {$charge->id}, pois ela possui envio de pagamento por PIX atrelado.",
            ]);
        }

        $userId = auth()->id();
        $meiId = session('mei_id');

        if ($charge->mei_id !== $meiId) {
            abort(403, 'Acesso negado: cobrança não pertence ao seu MEI.');
        }

        if ($charge->user_id !== $userId) {
            abort(403, 'Acesso negado: você não cadastrou essa cobrança.');
        }

        $charge->delete();

        return redirect()->route('cobrancas.index')->with('success', 'Cobrança excluída com sucesso.');
    }

}
