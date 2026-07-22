<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPaymentApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function build(): self
    {
        $this->order->loadMissing(['items', 'payment']);

        return $this
            ->subject('Pago recibido '.$this->order->folio)
            ->view('emails.admin.payment-approved')
            ->with([
                'adminOrderUrl' => route('admin.orders.show', $this->order),
            ]);
    }
}
