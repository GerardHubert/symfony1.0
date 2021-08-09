<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        dd("La page d'accueil fonctionne bien !");
    }

    /**
     * @Route("/test/{age<\d+>?inconnu}", name="test", methods={"GET", "POST"})
     */
    public function test(Request $request)
    {
        $age = $request->attributes->get("age", "inconnu");
        return new Response("Bonjour, ton age est $age ");
    }

    /**
     * @Route("/debug", name="debug")
     */
    public function debug()
    {
        dd("la page de deboggage fonctionne");
    }
}
