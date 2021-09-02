<?php

declare(strict_types=1);

namespace App\Cart;

use App\Entity\Product;

class CartItem
{
    public $product;
    private $quantity;

    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotal(): int
    {
        return $this->product->getPrice() * $this->quantity;
    }
}
