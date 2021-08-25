<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(): Response
    {
        $form = $this->createForm(LoginType::class);
        $formView = $form->createView();
        return $this->render('security/login.html.twig', [
            'formView' => $formView
        ]);
    }
}
