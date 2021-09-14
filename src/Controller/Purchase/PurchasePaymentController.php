<?php

declare(strict_types=1);

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    private $purchaseRepository;
    private $stripeService;

    public function __construct(PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {
        $this->purchaseRepository = $purchaseRepository;
        $this->stripeService = $stripeService;
    }

    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     * IsGranted("ROLE_USER")
     */
    public function showCardForm($id)
    {
        $purchase = $this->purchaseRepository->find($id);

        if (
            $purchase === null
            || ($purchase && $this->getUser() !== $purchase->getUser()
                || ($purchase->getStatus() === Purchase::STATUS_PAID))
        ) {
            return $this->redirectToRoute("cart_show");
        }

        $intent = $this->stripeService->createIntent($purchase);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
            'purchase' => $purchase,
            'stripePublicKey' => $this->stripeService->getStripePublicKey()
        ]);
    }
}
