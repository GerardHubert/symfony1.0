<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SendTokenToUser
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendToken(User $user)
    {
        $mail = new TemplatedEmail();

        $mail->from(new Address("no-reply@symshop.fr"))
            ->to(new Address($user->getEmail()))
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('email/send_token.html.twig')
            ->context([
                'user' => $user
            ]);

        $this->mailer->send($mail);
    }
}
