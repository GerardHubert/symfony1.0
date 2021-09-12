<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Product;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

class ProductViewEvent extends Event
{
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
    public function getProduct(): Product
    {
        return $this->product;
    }
}
