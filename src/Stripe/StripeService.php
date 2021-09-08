<?php

declare(strict_types=1);

namespace App\Stripe;

use App\Entity\Purchase;

class StripeService
{
    private $pk;
    private $sk;

    public function __construct(string $pk, string $sk)
    {
        $this->pk = $pk;
        $this->sk = $sk;
    }

    public function createIntent($purchase)
    {
        \Stripe\Stripe::setApiKey($this->sk);

        return \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur',
        ]);
    }
}
