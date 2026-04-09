<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function __construct(
        public $user,
        public array $items,
        public float $total
    ) {
    }

    public function build(): self
    {
        return $this->subject('Sillarri · Erosketa baieztapena')
            ->view('emails.shop-confirmation');
    }
}
