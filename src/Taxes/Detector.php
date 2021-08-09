<?php

declare(strict_types=1);

namespace App\Taxes;

class Detector
{

    private $seuil;

    public function __construct(float $seuil)
    {
        $this->seuil = $seuil;
    }

    public function detect(float $prix): bool
    {
        if ($prix > $this->seuil) {
            return true;
        }
        return false;
    }
}
