<?php

declare(strict_types=1);

namespace App\EventDispatcher;

use App\Event\PurchaseSuccessEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        // dump($purchaseSuccessEvent);
        $mail = new TemplatedEmail();
        $mail->from(new Address('no-reply@symshop.fr'))
            ->to(new Address($purchaseSuccessEvent->getPurchase()->getUser()->getEmail(), $purchaseSuccessEvent->getPurchase()->getUser()->getFullName()))
            ->subject('Confirnation de vore commande n° ' . $purchaseSuccessEvent->getPurchase()->getId())
            ->htmlTemplate('email/purchase_confirm.html.twig')
            ->context([
                'purchase' => $purchaseSuccessEvent->getPurchase()
            ]);

        $this->mailer->send($mail);
    }
}
