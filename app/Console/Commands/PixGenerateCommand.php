<?php

namespace App\Console\Commands;


use App\Mail\PagamentoEmail;

use App\Models\Payment;
use App\Models\Charge;
use App\Models\Client;
use App\Models\MeiProfile;

use Illuminate\Console\Command;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;   // 👈 importa o Mail

class PixGenerateCommand extends Command
{
    protected $signature = 'pix:gerar';
    protected $description = 'Gera um PIX de 1 centavo com QR Code em PDF';

    private function gerarPayloadPix($chave, $valor, $descricao, $cidade)
    {
        $format = function ($id, $conteudo) {
            $len = strlen($conteudo);
            return $id . str_pad($len, 2, '0', STR_PAD_LEFT) . $conteudo;
        };

        $gui = $format('00', 'BR.GOV.BCB.PIX');
        $chavePix = $format('01', $chave);
        $merchant = $format('26', $gui . $chavePix);
        $categoria = $format('52', '0000');
        $moeda = $format('53', '986');
        $valor = $format('54', number_format($valor, 2, '.', ''));
        $pais = $format('58', 'BR');
        $nome = $format('59', $descricao);
        $cidade = $format('60', strtoupper($cidade));
        $adicional = $format('62', '0503***');

        $payloadSemCRC = '000201' .
            $merchant .
            $categoria .
            $moeda .
            $valor .
            $pais .
            $nome .
            $cidade .
            $adicional .
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
        // Busca pagamentos que ainda não foram enviados
        $payments = Payment::with(['mei', 'charge', 'client'])
            ->where('sent_at', null)
            ->get();

        foreach ($payments as $payment) {

            $mei = $payment->mei;         // dados do MEI
            $charge = $payment->charge;   // dados da cobrança
            $client = $payment->client;   // dados do cliente

            // Monta objeto para envio
            $cobranca = new \stdClass();
            $cobranca->id = $payment->id;
            $cobranca->cliente_email = $client->email;
            $cobranca->cliente_nome = $client->name ?? 'Cliente';   // 👈 adiciona o nome do cliente
            $cobranca->emitente = $mei->name ?? $mei->fantasia ?? 'Emitente';
            $cobranca->chave_pix = $mei->identification; // ou a chave que você armazenar
            $cobranca->valor = $payment->amount;
            $cobranca->descricao = $charge->description;
            $cobranca->cidade = $mei->city;
            $cobranca->cnpj = $mei->cnpj;
            $cobranca->identification = $mei->identification;

            // Gera payload PIX
            $payload = $this->gerarPayloadPix(
                $cobranca->chave_pix,
                $cobranca->valor,
                $cobranca->descricao,
                $cobranca->cidade
            );

            // Gera QR Code
            $qrPath = storage_path("app/qrcode_pix_{$cobranca->id}.png");
            $options = new QROptions(['eccLevel' => QRCode::ECC_L, 'scale' => 5]);
            (new QRCode($options))->render($payload, $qrPath);

            // 👇 adiciona isso aqui
            $cobranca->pix_codigo = $payload;
            // Gera PDF
            $html = "
            <h1>PIX de {$cobranca->valor}</h1>
            <p><strong>Descrição:</strong> {$cobranca->descricao}</p>
            <p><strong>Chave Pix:</strong> {$cobranca->chave_pix}</p>
            <p>Escaneie o QR Code abaixo ou use o código copia e cola:</p>
            <img src='{$qrPath}' style='width:200px;'>
            <pre style='font-size:10px; white-space: pre-wrap;'>{$payload}</pre>
        ";
            $pdfPath = storage_path("app/pix_{$cobranca->id}.pdf");
            Pdf::loadHTML($html)->save($pdfPath);

            // Envia e-mail
            Mail::to($cobranca->cliente_email)
                ->send(new PagamentoEmail($cobranca, $pdfPath));

            $this->info("E-mail enviado para {$cobranca->cliente_email}");

            // Marca como enviado
            $payment->user_id_sent = 1;
            $payment->sent_at = now();
            $payment->save();
        }
    }
}
