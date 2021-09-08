<?php

declare(strict_types=1);

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{
    private $purchaseRepo;
    private $cartService;

    public function __construct(PurchaseRepository $purchaseRepo, CartService $cartService, EntityManagerInterface $em)
    {
        $this->purchaseRepo = $purchaseRepo;
        $this->cartService = $cartService;
        $this->em = $em;
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
        $this->addFlash('success', 'Votre commande a bien été confirmée et payée !');
        return $this->redirectToRoute("purchase_index");
    }
}
