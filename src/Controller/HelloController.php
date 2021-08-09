<?php

declare(strict_types=1);

namespace App\Controller;

use App\Taxes\Detector;
use App\Taxes\Calculator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{

    private $calculator;
    private $detector;

    public function __construct(Calculator $calculator, Detector $detector)
    {
        $this->calculator = $calculator;
        $this->detector = $detector;
    }

    /**
     * @Route("/hello/{prenom<\w+>?World}", name="hello", methods={"GET", "POST"})
     */
    public function hello($prenom)
    {
        $taxe = $this->calculator->calcul(200);
        dump($this->detector->detect(10));
        dump($this->detector->detect(526));
        dump($taxe);
        return new Response("Hello $prenom");
    }
}
