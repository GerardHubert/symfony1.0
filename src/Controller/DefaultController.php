<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function index()
    {
        dd("Voici la page accueil");
    }

    /**
     * @Route("/test/{age}", name="test", methods={"GET", "POST"}, defaults="inconnu",
     * requirements="\d+", host="localhost")
     */
    public function test(Request $request)
    {
        $age = $request->query->get("age", "inconnu");
        return new Response("Bonjour, ton age est $age ");
    }
}
