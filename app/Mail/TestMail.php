<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly string $fromName,
        private readonly string $fromAddress,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Prueba de correo - GO Center')
            ->view('emails.test')
            ->with([
                'fromName' => $this->fromName,
                'fromAddress' => $this->fromAddress,
            ]);
    }
}
