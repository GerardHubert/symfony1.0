<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Taxes\Calculator;

class HelloController
{

    private $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @Route("/hello/{prenom<\w+>?World}", name="hello", methods={"GET", "POST"})
     */
    public function hello($prenom)
    {
        $taxe = $this->calculator->calcul(200);
        dump($taxe);
        return new Response("Hello $prenom");
    }
}
