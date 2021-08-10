<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{

    /**
     * @Route("/hello/{prenom<\w+>?World}", name="hello", methods={"GET", "POST"})
     */
    public function hello($prenom)
    {
        return $this->render('hello.html.twig', [
            'prenom' => $prenom,
        ]);
    }
}
