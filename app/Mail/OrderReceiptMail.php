<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class OrderReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public bool $paymentReceived = false,
    )
    {
    }

    public function build(): self
    {
        $this->order->loadMissing(['items', 'payment']);

        return $this
            ->subject(($this->paymentReceived ? 'Pago recibido ' : 'Recibo de pedido ').$this->order->folio)
            ->view('emails.orders.receipt')
            ->with([
                'orderUrl' => URL::signedRoute('orders.public.show', $this->order),
                'bank' => config('services.bank_transfer'),
                'oxxo' => config('services.oxxo_payment'),
                'paymentReceived' => $this->paymentReceived,
            ]);
    }
}
