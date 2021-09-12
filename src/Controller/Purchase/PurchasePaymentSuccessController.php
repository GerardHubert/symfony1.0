<?php

declare(strict_types=1);

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController
{
    private $purchaseRepo;
    private $cartService;
    private $dispatcher;

    public function __construct(PurchaseRepository $purchaseRepo, CartService $cartService, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->purchaseRepo = $purchaseRepo;
        $this->cartService = $cartService;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/purchase/success/{id}", name="purchase_payment_success")
     * IsGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function success($id)
    {
        $purchase = $this->purchaseRepo->find($id);

        if (
            $purchase === null
            || ($purchase && $this->getUser() !== $purchase->getUser()
                || ($purchase->getStatus() === Purchase::STATUS_PAID))
        ) {
            $this->addFlash('warning', "Cette commande n'existe pas");
            return $this->redirectToRoute("cart_show");
        }

        $purchase->setStatus(Purchase::STATUS_PAID);
        $this->em->flush();
        $this->cartService->clear();

        // on crée un nouvel évènement correspondant au succès de la commande (paiement)
        // en passant la purchase à l'évènement
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        // on dispatche l'évènement en passant : l'évènement, le nom de l'évènement
        $this->dispatcher->dispatch($purchaseEvent, 'purchase.success');

        $this->addFlash('success', 'Votre commande a bien été confirmée et payée !');
        return $this->redirectToRoute("purchase_index");
    }
}
