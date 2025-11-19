<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Charge;

class ProofController extends Controller
{
    public function show($key)
    {
        // Busca o pagamento com os relacionamentos carregados
        $payment = Payment::with(['client', 'mei', 'charge', 'user', 'resentBy'])
            ->where('key', $key)
            ->first();

        if (!$payment) {
            abort(404);
        }

        return Inertia::render('Comprovante', [
            'data' => [
                'key' => $payment->key,
                'charge_id' => $payment->charge_id,
                'pix_key' => $payment->pix_key,
                'amount' => $payment->amount,
                'description' => $payment->charge?->description,
                'client_name' => $payment->client?->name,
                'client_cpf_cnpj' => $payment->client?->cpf_cnpj,
                'mei_identification' => $payment->mei?->identification,
                'mei_cnpj' => $payment->mei?->cnpj,
                'mei_email' => $payment->mei?->email,
                'mei_phone' => $payment->mei?->phone,
                'ies_paid' => !empty($payment->return_at),
            ],
        ]);
    }

    public function upload(Request $request, $key)
    {
        // Validação do arquivo
        $request->validate([
            'comprovante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'comprovante.required' => 'É necessário anexar um comprovante.',
            'comprovante.mimes' => 'O comprovante deve ser PDF, JPG ou JPEG.',
            'comprovante.max' => 'O arquivo não pode ultrapassar 5MB.',
        ]);

        // Busca o pagamento
        $payment = Payment::where('key', $key)->firstOrFail();

        // Salva o arquivo no S3 em payments/{key}/
        $directory = "payments/{$payment->key}";
        $file = $request->file('comprovante');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Armazena no S3
        $path = $file->storeAs($directory, $filename, 's3');

        // Torna o arquivo público
        \Storage::disk('s3')->setVisibility($path, 'public');

        // Salva o path no payment
        $payment->comprovante_path = $path;

        $payment->status = 3;
        $payment->return_at = now();
        $payment->save();

        // Atualiza o charge pai
        if ($payment->charge_id) {
            $charge = Charge::find($payment->charge_id);
            if ($charge) {
                $charge->status = 2;
                $charge->payment_date = now();
                $charge->save();
            }
        }


        return Inertia::render('Comprovante', [
            'data' => [
                'ies_paid' => true,
            ],
        ]);
    }

}
