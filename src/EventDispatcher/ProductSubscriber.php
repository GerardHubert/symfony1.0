<?php

declare(strict_types=1);

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ProductSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'product.view' => 'logView'
        ];
    }

    public function logView(ProductViewEvent $productViewEvent)
    {
        // $productName = $productViewEvent->getProduct()->getName();

        // $mail = new TemplatedEmail();
        // $mail->from(new Address('symshop@symshop.fr'))
        //     ->to(new Address('admin@symshop.fr'))
        //     ->subject("Consultation du produit " . $productViewEvent->getProduct()->getId())
        //     ->htmlTemplate('email/product_view.html.twig')
        //     ->context([
        //         'product' => $productViewEvent->getProduct()
        //     ]);

        // $this->mailer->send($mail);
    }
}
