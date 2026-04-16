<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderAdminAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function __construct(
        public ShopOrder $order,
        public array $items,
        public float $total
    ) {
    }

    public function build(): self
    {
        return $this->subject('Sillarri · Pedido pendiente de comprobacion')
            ->view('emails.shop-admin-alert');
    }
}
