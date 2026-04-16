<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public const TRANSFER_ACCOUNT_HOLDER = 'Destino Roko CAJA R. DE NAVARRA, S.C.C.';
    public const TRANSFER_IBAN = 'ES06 3008 0266 5143 2886 2125';

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function __construct(
        public $user,
        public array $items,
        public float $total,
        public ?int $orderId = null
    ) {
    }

    public function build(): self
    {
        return $this->subject('Sillarri · Erosketa baieztapena')
            ->view('emails.shop-confirmation');
    }
}
