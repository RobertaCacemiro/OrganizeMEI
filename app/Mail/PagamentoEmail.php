<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PagamentoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $cobranca;

    public function __construct($cobranca)
    {
        $this->cobranca = $cobranca;
    }

    public function build()
    {
        return $this->subject("Cobrança Nº {$this->cobranca->id}")
            ->view('emails.payments');
    }
}
