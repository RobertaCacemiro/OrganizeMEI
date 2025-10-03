<?php

namespace App\Console\Commands;

use App\Mail\PagamentoEmail;
use App\Models\Payment;
use Illuminate\Console\Command;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PixGenerateCommand extends Command
{
    protected $signature = 'pix:gerar';
    protected $description = 'Gera um PIX e envia e-mail de cobrança';

    private function gerarPayloadPix($chave, $valor, $descricao, $cidade)
    {
        $format = function ($id, $conteudo) {
            $len = strlen($conteudo);
            return $id . str_pad($len, 2, '0', STR_PAD_LEFT) . $conteudo;
        };

        $sanitize = function ($text) {
            $cleanText = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
            $cleanText = preg_replace('/[^A-Za-z0-9 ]/', '', $cleanText);
            return strtoupper($cleanText);
        };

        $gui = $format('00', 'BR.GOV.BCB.PIX');
        $chavePix = $format('01', $chave);
        $merchant = $format('26', $gui . $chavePix);
        $categoria = $format('52', '0000');
        $moeda = $format('53', '986');
        $valor = $format('54', number_format((float) $valor, 2, '.', ''));
        $pais = $format('58', 'BR');
        $nome = $format('59', $sanitize(substr($descricao, 0, 25)));
        $cidade = $format('60', $sanitize($cidade));

        $payloadSemCRC = '000201' .
            $merchant .
            $categoria .
            $moeda .
            $valor .
            $pais .
            $nome .
            $cidade .
            '6304';

        $crc = strtoupper(dechex($this->crc16($payloadSemCRC)));

        return $payloadSemCRC . str_pad($crc, 4, '0', STR_PAD_LEFT);
    }

    private function crc16($str)
    {
        $crc = 0xFFFF;
        for ($c = 0; $c < strlen($str); $c++) {
            $crc ^= ord($str[$c]) << 8;
            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = $crc << 1;
                }
                $crc &= 0xFFFF;
            }
        }
        return $crc;
    }

    public function handle()
    {
        Log::channel('pix_cron')->info("Mensagem de teste");

        $payments = Payment::with(['mei', 'charge', 'client'])
            ->whereNull('sent_at')
            ->get();

        foreach ($payments as $payment) {
            try {

                $payment->status = 0;
                $payment->save();


                Log::channel('pix_cron')->info("Processando pagamento ID {$payment->id}");

                $mei = $payment->mei;
                $charge = $payment->charge;
                $client = $payment->client;

                if (!$payment->key) {
                    $payment->key = Str::uuid()->toString();
                    $payment->save();
                }

                // Busca chave PIX ativa
                $pixKey = $mei->activePixKey()
                    ->where('is_active', 1)
                    ->first();

                if (!$pixKey) {
                    Log::channel('pix_cron')->info("Nenhuma chave PIX ativa encontrada para o MEI {$mei->id}");
                    throw new \Exception("Nenhuma chave PIX ativa encontrada para o MEI {$mei->id}");
                }

                // // Monta objeto de cobrança
                $cobranca = new \stdClass();
                $cobranca->id = $charge->id;
                $cobranca->key = $payment->key;
                $cobranca->cliente_email = $client->email;
                $cobranca->cliente_nome = $client->name;
                $cobranca->chave_pix = $pixKey->key_value;
                $cobranca->valor = $payment->amount;
                $cobranca->descricao = $charge->description;
                $cobranca->cidade = $mei->city;
                $cobranca->cpf_cnpj = $mei->cnpj;
                $cobranca->identification = $mei->identification;

                // $cobranca->user_id = $payment->user_id;

                // // Gera payload PIX
                // $payload = $this->gerarPayloadPix(
                //     $cobranca->chave_pix,
                //     $cobranca->valor,
                //     $cobranca->descricao,
                //     $cobranca->cidade
                // );

                // $cobranca->pix_codigo = $payload;

                // //Debug
                // Log::channel('pix_cron')->info("Tentando enviar e-mail para {$cobranca->cliente_email}");


                // // Envia e-mail
                // Mail::to($cobranca->cliente_email)
                //     ->send(new PagamentoEmail($cobranca));

                // $this->info("E-mail enviado para {$cobranca->cliente_email}");
                // Log::channel('pix_cron')->info("E-mail enviado para {$cobranca->cliente_email}");

                // Marca como enviado
                $payment->user_id_sent = $cobranca->user_id;
                $payment->pix_key = $cobranca->pix_codigo;
                $payment->sent_at = now();
                $payment->status = 2;
                $payment->error_message = null;
                $payment->save();

            } catch (\Exception $e) {

                Log::channel('pix_cron')->info("ERRO no pagamento ID {$payment->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");


                $payment->error_message = $e->getMessage();
                $payment->status = 4;
                $payment->save();

                $this->error("Falha no envio do pagamento ID {$payment->id}: " . $e->getMessage());
            }
        }

        $payment->status = 5;
        $payment->save();



        Log::channel('pix_cron')->info("[" . now() . "] PixGenerateCommand finalizado\n");


    }
}
