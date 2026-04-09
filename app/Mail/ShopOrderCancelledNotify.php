<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderCancelledNotify extends Mailable
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
        return $this->subject('Sillarri · Eskaria ezeztatua')
            ->view('emails.shop-cancel-notify');
    }
}
