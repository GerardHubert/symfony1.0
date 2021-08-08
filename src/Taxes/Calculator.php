<?php

declare(strict_types=1);

namespace App\Taxes;

class Calculator
{
    public function calcul(float $prix): float
    {
        $taxe = $prix * 20 / 100;
        return $taxe;
    }
}
