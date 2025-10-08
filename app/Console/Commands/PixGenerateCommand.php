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
        $this->info("--- Iniciando o comando pix:gerar ---");
        Log::info("Início do PixGenerateCommand.");

        try {
            // 1. VERIFICAÇÃO DE CONSULTA (DATA)
            $payments = Payment::with(['mei', 'charge', 'client'])
                ->whereNull('sent_at')
                ->get();

            $count = $payments->count();
            $this->warn("Total de pagamentos encontrados para envio: {$count}");
            Log::info("Pagamentos a serem processados: {$count}.");

            if ($count === 0) {
                $this->info("Nenhum pagamento pendente. Encerrando.");
                return;
            }

            foreach ($payments as $payment) {
                $this->info("Processando pagamento ID: {$payment->id}");

                // 2. PRIMEIRO PONTO DE SALVAMENTO NO BANCO
                // Se esta linha falhar, o erro DEVE ser capturado pelo catch.
                try {
                    if (!$payment->key) {
                        $payment->key = Str::uuid()->toString();
                    }

                    $payment->processing_at = now();
                    $payment->status = 6; // Erro
                    $payment->save();

                    $this->info("Pagamento ID {$payment->id} salvo como 'processing'.");
                    Log::info("Pagamento ID {$payment->id} marcado como 'processing'.");

                    // Resto da lógica (PIX, e-mail)
                    $mei = $payment->mei;
                    $charge = $payment->charge;
                    $client = $payment->client;

                    $pixKey = $mei->activePixKey()
                        ->where('is_active', 1)
                        ->first();

                    if (!$pixKey) {
                        throw new \Exception("Nenhuma chave PIX ativa encontrada para o MEI {$mei->id}");
                    }

                    // Monta objeto de cobrança... (seu código continua aqui)
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
                    $cobranca->user_id = $payment->user_id; // Adicionei isso, pois você usava user_id_sent abaixo

                    $payload = $this->gerarPayloadPix(
                        $cobranca->chave_pix,
                        $cobranca->valor,
                        $cobranca->descricao,
                        $cobranca->cidade
                    );

                    $cobranca->pix_codigo = $payload;
                    $this->info("Payload PIX gerado para ID {$payment->id}.");

                    // 3. PONTO DE ENVIO DE E-MAIL
                    Mail::to($cobranca->cliente_email)
                        ->send(new PagamentoEmail($cobranca));

                    $this->info("E-mail enviado com sucesso para {$cobranca->cliente_email}");
                    Log::info("E-mail enviado para {$cobranca->cliente_email}.");

                    // 4. ÚLTIMO PONTO DE SALVAMENTO NO BANCO
                    $payment->user_id_sent = $cobranca->user_id;
                    $payment->pix_key = $cobranca->pix_codigo;
                    $payment->sent_at = now();
                    $payment->status = 2;
                    $payment->processing_at = null;
                    $payment->error_message = null;
                    $payment->save();

                    $this->info("Pagamento ID {$payment->id} marcado como 'enviado'.");
                    Log::info("Pagamento ID {$payment->id} marcado como 'enviado'.");

                } catch (\Exception $e) {
                    // CATCH DENTRO DO LOOP (Erros de PIX ou E-mail)
                    $payment->error_message = $e->getMessage();
                    $payment->status = 0; // Erro
                    $payment->processing_at = null; // Libera o processamento
                    $payment->save();

                    $this->error("Falha no envio do pagamento ID {$payment->id}: " . $e->getMessage());
                    Log::error("ERRO ao processar Pagamento ID {$payment->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $payment->status = 5; // Erro

            // CATCH FORA DO LOOP (Erros de Conexão ou Consulta)
            $this->error("ERRO FATAL DE CONEXÃO OU CONSULTA: " . $e->getMessage());
            Log::critical("ERRO FATAL: " . $e->getMessage());
        }

        $this->info("--- Comando pix:gerar finalizado ---");
    }
}
