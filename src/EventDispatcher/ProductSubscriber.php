<?php

declare(strict_types=1);

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'product.view' => 'logView'
        ];
    }

    public function logView(ProductViewEvent $productViewEvent)
    {
        $productName = $productViewEvent->getProduct()->getName();
        $this->logger->info("un utilisateur consulte le produit : " . $productName);
    }
}
