<?php

namespace App\Mail;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use SendGrid;
use SendGrid\Mail\Mail as SendGridMail;
use Psr\Log\LoggerInterface;

class SendGridTransport extends AbstractTransport
{
    protected $sendgrid;
    protected $logger;

    public function __construct(string $apiKey, LoggerInterface $logger)
    {
        $this->sendgrid = new SendGrid($apiKey);
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * Método obrigatório para compatibilidade com AbstractTransport
     */
    protected function doSend(SentMessage $sentMessage): void
    {
        $email = $sentMessage->getOriginalMessage();

        $message = new SendGridMail();

        $from = $email->getFrom()[0] ?? null;
        if ($from) {
            $message->setFrom($from->getAddress(), $from->getName());
        }

        foreach ($email->getTo() as $to) {
            $message->addTo($to->getAddress(), $to->getName());
        }

        $message->setSubject($email->getSubject());
        $message->addContent('text/html', $email->getHtmlBody() ?? $email->getTextBody());

        try {
            $response = $this->sendgrid->send($message);
            $this->logger->info('E-mail enviado via SendGrid API', [
                'status' => $response->statusCode(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao enviar e-mail via SendGrid API: ' . $e->getMessage());
        }
    }

    public function __toString(): string
    {
        return 'sendgrid';
    }
}
