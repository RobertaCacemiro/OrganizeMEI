<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

use App\Models\Charge;
use App\Models\Client;
use App\Models\Payment;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        // Lista de clientes separados
        $clients = Client::where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->orderBy('name')
            ->get(['id', 'cpf_cnpj', 'name'])
            ->map(function ($client) {
                return [
                    'value' => $client->id,
                    'label' => $client->cpf_cnpj . "- " . $client->name,
                ];
            });

        $statusMap = [
            1 => 'Pendente Envio',
            2 => 'Pendente Pagamento',
            3 => 'Pago',
            4 => 'Vencido',
        ];

        // Cobranças com cliente
        $query = Charge::with('client')
            ->where('user_id', $userId)
            ->where('mei_id', $meiId);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('ies_send_pix')) {
            $query->where('ies_send_pix', $request->ies_send_pix);
        }

        $charges = $query->orderBy('id', 'desc')
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
                    'link_acesso_pix' => ($charge->ies_send_pix) ? "Sim" : "Não",
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
            'filters' => $request->all()
        ]);
    }

    public function getDashboardValues()
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $query = Charge::where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereMonth('due_date', $currentMonth)
            ->whereYear('due_date', $currentYear);

        // Contagens por status
        $countPendentes = (clone $query)->where('status', 2)->count();
        $countPagos = (clone $query)->where('status', 3)->count();
        $countAtrasados = (clone $query)->where('status', 4)->count();

        return [
            'total_cobrancas' => $countPendentes + $countPagos + $countAtrasados,
            'total_pagos' => $countPagos,
            'total_pendentes' => $countPendentes,
            'total_atrasados' => $countAtrasados,
        ];
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
                'ies_send_pix' => 'required|boolean',
                'description' => 'nullable|string|max:255',
                'payment_date' => 'nullable|date',
            ]);

            $userId = auth()->id();
            $meiId = session('mei_id');
            $access = session('access');

            DB::beginTransaction();

            $today = date('Y-m-d');

            if (!empty($validated['payment_date'])) {
                $validated['status'] = 3; // Pago

            } else {
                if ($validated['due_date'] < $today) {
                    $validated['status'] = 4; // Vencido

                } elseif ($validated['ies_send_pix']) {
                    $validated['status'] = 1; // Aguardando PIX

                } else {
                    $validated['status'] = 2; // Aberto
                }
            }


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

            if ($validated['ies_send_pix']) {

                if (!in_array($access, [1, 2])) {
                    DB::rollBack();
                    return back()->withErrors('error', 'O envio de PIX está disponível apenas para clientes Premium.');
                }

                if (empty($validated['payment_date'])) {
                    Payment::create([
                        'user_id' => $userId,
                        'mei_id' => $meiId,
                        'client_id' => $validated['client_id'],
                        'charge_id' => $charge->id,
                        'amount' => $validated['amount'],
                        'due_date' => $validated['due_date'],
                    ]);
                } else {
                    DB::rollBack();
                    return back()->withErrors('Não é possível enviar PIX caso o pagamento já tenha sido efetuado!');
                }
            }

            DB::commit();

            return redirect()->back()->with('success', $validated['ies_send_pix']
                ? 'Cobrança cadastrada e e-mail de PIX na fila de envio!'
                : 'Cobrança cadastrada com sucesso!');


        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('error', 'Erro ao salvar a cobrança. ' . $e->getMessage());
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

        try {
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

                $mensagemPixEnviado = false;

                $pixOriginal = $charge->ies_send_pix;  // antes
                $pixNovo = $validated['ies_send_pix']; // depois

                $dataPagOriginal = $charge->payment_date;
                $dataPagNova = $validated['payment_date'];

                $payment = Payment::where('charge_id', $charge->id)->first();

                /*
                |--------------------------------------------------------------------------
                | 1. PIX era NÃO → virou SIM
                |--------------------------------------------------------------------------
                */
                if (!$pixOriginal && $pixNovo) {

                    // (a) Informou data de pagamento agora -> PIX não pode ser enviado
                    if (!empty($dataPagNova) || !empty($dataPagOriginal)) {
                        DB::rollBack();
                        return back()->withErrors(
                            'error',
                            'Cobrança marcada como paga. Não é possível enviar PIX porque ela já foi paga.'
                        );
                    }

                    // (b) Não informou payment_date → criar Payment
                    if (!$payment) {
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

                /*
                |--------------------------------------------------------------------------
                | 2. PIX era SIM → continua SIM
                |--------------------------------------------------------------------------
                */
                if ($pixOriginal && $pixNovo) {

                    // Se usuario tinha data de pagamento e agora apagou → bloquear
                    if (!empty($dataPagOriginal) && empty($dataPagNova)) {
                        DB::rollBack();
                        return back()->withErrors(
                            'error',
                            'Cobrança marcada como paga. Não é possível enviar PIX porque ela já foi paga.'
                        );
                    }

                    // Se informou data de pagamento agora
                    if (!empty($dataPagNova)) {

                        // Se existe Payment pendente
                        if ($payment && empty($payment->processing_at) && $payment->status == 1) {
                            $payment->update([
                                'processing_at' => now(),
                                'return_at' => now(),
                                'status' => 5, // cancelado
                            ]);
                            $validated['status'] = 3; // Pago

                        } else if ($payment && !empty($payment->processing_at) && $payment->status != 3) {
                            $payment->update([
                                'return_at' => now(),
                                'status' => 6, // pago manualmente
                            ]);
                            $validated['status'] = 3; // Pago

                            $mensagemPixEnviado = true;
                        }
                    }

                    // Se não tem payment_date → garantir Payment existente
                    if (empty($dataPagNova) && !$payment) {
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

                /*
                |--------------------------------------------------------------------------
                | 3. PIX está sendo DESATIVADO (SIM → NÃO)
                |--------------------------------------------------------------------------
                | Pode manter a lógica existente ou colocar regras extras aqui caso desejar.
                |--------------------------------------------------------------------------
                */

                // Atualiza cobrança
                $charge->update($validated);

                DB::commit();

                if ($mensagemPixEnviado) {
                    return back()->with(
                        'success',
                        'Cobrança marcada como paga. O PIX já havia sido enviado anteriormente.'
                    );
                }

                return back()->with(
                    'success',
                    $pixNovo ? 'Cobrança atualizada e e-mail de PIX na fila de envio!'
                    : 'Cobrança atualizada com sucesso!'
                );

            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->withErrors('Não foi possível atualizar a cobrança. Tente novamente mais tarde.');
            }


        } catch (\Throwable $e) {
            return back()->withErrors(
                'error',
                'Erro ao salvar a cobrança. ' . $e->getMessage()
            );
        }
    }

    public function destroy($id)
    {
        try {

            $charge = Charge::findOrFail($id);

            // Buscar o pagamento associado
            $payment = Payment::where('charge_id', $charge->id)->first();

            // BLOQUEIO: se o payment estiver enviado por PIX
            if ($payment && $payment->status == 2 || !empty($payment->sent_at)) {
                return back()->withErrors(
                    'error',
                    "Não foi possível excluir a cobrança {$charge->id}, pois a cobrança por e-mail já foi realizada."
                );
            }

            $userId = auth()->id();
            $meiId = session('mei_id');

            if ($charge->mei_id !== $meiId) {
                abort(403, 'Acesso negado: cobrança não pertence ao seu MEI.');
            }

            if ($charge->user_id !== $userId) {
                abort(403, 'Acesso negado: você não cadastrou essa cobrança.');
            }

            // Se existir payment e não estiver bloqueado → excluir também
            if ($payment) {
                $payment->delete();
            }

            // Excluir a cobrança
            $charge->delete();

            return redirect()->back()->with('success', 'Cobrança excluída com sucesso!');

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao excluir a cobrança: ' . $e->getMessage());
        }

    }

}
