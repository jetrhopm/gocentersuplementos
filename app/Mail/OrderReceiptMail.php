<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class OrderReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function build(): self
    {
        $this->order->loadMissing(['items', 'payment']);

        return $this
            ->subject('Recibo de pedido '.$this->order->folio)
            ->view('emails.orders.receipt')
            ->with([
                'orderUrl' => URL::signedRoute('orders.public.show', $this->order),
            ]);
    }
}
