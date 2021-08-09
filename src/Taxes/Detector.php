<?php

declare(strict_types=1);

namespace App\Taxes;

class Detector
{
    public function detect(int $amount): bool
    {
        if ($amount <= 100) {
            return false;
        } elseif ($amount > 100) {
            return true;
        }
    }
}
