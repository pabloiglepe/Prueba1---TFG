<?php

namespace App\Mail;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Http;

class BrevoTransport extends AbstractTransport
{
    public function __construct(protected string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $to = [];
        foreach ($email->getTo() as $address) {
            $to[] = [
                'email' => $address->getAddress(),
                'name'  => $address->getName() ?: $address->getAddress(),
            ];
        }

        $payload = [
            'sender' => [
                'email' => $email->getFrom()[0]->getAddress(),
                'name'  => $email->getFrom()[0]->getName() ?: config('app.name'),
            ],
            'to'          => $to,
            'subject'     => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() ?: $email->getTextBody(),
            'textContent' => $email->getTextBody(),
        ];

        $response = Http::withHeaders([
            'api-key'      => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new \Exception('Brevo API error: ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}