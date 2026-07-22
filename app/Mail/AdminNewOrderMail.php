<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNewOrderMail extends Mailable implements ShouldQueue
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
            ->subject('Nuevo pedido '.$this->order->folio)
            ->view('emails.admin.new-order')
            ->with([
                'adminOrderUrl' => route('admin.orders.show', $this->order),
            ]);
    }
}
