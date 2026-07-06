<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function build(): self
    {
        $this->order->loadMissing(['items', 'payment']);

        return $this
            ->subject('Tu pedido '.$this->order->folio.' esta pendiente de pago')
            ->view('emails.orders.payment-reminder')
            ->with([
                // Enlace firmado: abre el pedido con toda la informacion y el
                // boton de pago, sin que el cliente teclee folio ni correo.
                'orderUrl' => URL::signedRoute('orders.public.show', $this->order),
            ]);
    }
}
