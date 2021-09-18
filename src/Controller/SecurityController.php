<?php

namespace App\Controller;

use App\Form\LoginType;
use App\Form\SigninType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $form = $this->createForm(LoginType::class, ['email' => $utils->getLastUsername()]);

        $authenticationError = $utils->getLastAuthenticationError();

        $formView = $form->createView();
        return $this->render('security/login.html.twig', [
            'formView' => $formView,
            'error' => $authenticationError
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/admin/home", name="security_adminPage")
     */
    public function adminPage()
    {
        return $this->render('admin_home.html.twig', []);
    }

    /**
     * @Route("/signin", name="security_signin")
     */
    public function signin(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        //1. On affiche le formlaire d'inscription
        $form = $this->createForm(SigninType::class);

        $form->handleRequest($request);

        //2. Si le formulaire a été soumis est valide, on enregistre le nouvel utilisateur
        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();

            // Vérifier qu'il n'existe pas déjà un utilisateur avec la même adresse mail
            if ($userRepository->findOneBy(['email' => $newUser->getEmail()]) != null) {
                $this->addFlash('danger', 'Cette adresse mail existe déjà');
                return $this->redirectToRoute('security_signin');
            };

            // Si pas de doublon sur unique key, on créer véritablement notre user et on l'enregistre en bdd
            $newUser->setPassword(password_hash($newUser->getPassword(), PASSWORD_ARGON2I))
                ->setRoles(['ROLE_USER']);

            $em->persist($newUser);
            $em->flush();

            $this->addFlash("success", "Votre compte a bien été créé. Vous pouvez vous connecter avec l'adresse email et le mot de passe indiqués");

            return $this->redirectToRoute("security_login");
        }

        return $this->render('security/signin.html.twig', [
            'formView' => $form->createView()
        ]);
    }
}
