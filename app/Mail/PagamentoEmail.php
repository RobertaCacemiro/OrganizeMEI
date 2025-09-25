<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class PagamentoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $cobranca;
    public $pdfPath;
    public $qrcodeCid;

    public function __construct($cobranca, $pdfPath)
    {
        $this->cobranca = $cobranca;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        // gera QR temporário em arquivo
        $options = new QROptions([
            'eccLevel' => QRCode::ECC_L,
            'scale' => 5,
        ]);

        $qrPath = public_path("storage/qrcodes/qrcode_{$this->cobranca->id}.png");
        $options = new QROptions(['eccLevel' => QRCode::ECC_L, 'scale' => 5]);
        (new QRCode($options))->render($this->cobranca->pix_codigo, $qrPath);

        // embute a imagem no e-mail usando Symfony Mailer
        $this->withSymfonyMessage(function ($message) use ($qrPath) {
            $this->qrcodeCid = $message->embedFromPath($qrPath, 'qrcode_pix', 'image/png');
        });

        $qrcodeUrl = asset("storage/qrcodes/qrcode_{$this->cobranca->id}.png");

        return $this->subject("Cobrança PIX - {$this->cobranca->descricao}")
            ->view('emails.payments')
            ->with([
                'cobranca' => $this->cobranca,
                'qrcodeUrl' => $qrcodeUrl,
            ])
            ->attach($this->pdfPath, [
                'as' => "pix_{$this->cobranca->id}.pdf",
                'mime' => 'application/pdf',
            ]);
    }
}
