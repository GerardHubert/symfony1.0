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
use App\Form\ForgottenPassType;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use App\Security\SendTokenToUser;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SecurityController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

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

    /**
     * @Route("/forgottenPassword", name="security_forgottenPassword")
     */
    public function forgottenPassword(Request $request, UserRepository $userRepository, SendTokenToUser $sendToken, EntityManagerInterface $em)
    {
        // Création du formulaire
        $form = $this->createForm(ForgottenPassType::class);

        $form->handleRequest($request);

        // Vérification que le formulaire est valide et que l'adresse mail renvoie bien un user
        if ($request->getMethod() === 'POST' && $form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email' => $form->getData()]);

            if ($user === null) {
                $this->addFlash('danger', "L'adresse " . $form->getData()['email'] . " est inconnue");
                return $this->redirectToRoute("security_forgottenPassword");
            }

            // Si user trouvé, on envoie un lien par mail à l'utilisateur
            // On définit un token

            $token = uniqid();

            $user->setToken($token);
            $em->flush();

            $sendToken->sendToken($user);
            $this->addFlash('success', 'Un mail à l\'adresse indiquée vient de vous être envoyé');
            return $this->redirectToRoute('security_forgottenPassword');
        }

        return $this->render('security/forgotten_pass_page.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/security/resetPass/{userId}/{token}", name="security_resetPass")
     */
    public function resetPass($userId, $token, UserRepository $userRepository)
    {
        return $this->render('security/reset_pass_page.html.twig', [
            'userId' => $userId,
            'token' => $token
        ]);
    }
}
