<?php

declare(strict_types=1);

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'addPrenomToAttributes',
            'kernel.request' => 'addTown',
            'kernel.controller' => 'addAge'
        ];
    }
    public function addPrenomToAttributes(RequestEvent $requestEvent)
    {
        $requestEvent->getRequest()->attributes->set('prenom', 'Gérard');
    }

    public function addTown(RequestEvent $requestEvent)
    {
        $this->logger->info('town');
    }

    public function addAge(ControllerEvent $controllerEvent)
    {
        $this->logger->info('age');
    }
}
