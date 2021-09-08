<?php

declare(strict_types=1);

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Purchase\PurchasePersister;

class PurchaseConfirmationController extends AbstractController
{
    private $cartService;
    private $purchasePersister;

    public function __construct(CartService $cartService, PurchasePersister $purchasePersister)
    {
        $this->cartService = $cartService;
        $this->purchasePersister = $purchasePersister;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER")
     */
    public function confirm(Request $request)
    {
        // Récupérer le formulaire
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        // Si le formulaire n'a pas été soumis: message flash et redirection
        if ($form->isSubmitted() === false) {
            $this->addFlash('warning', "Merci de remplir le formulaire de confimation");
            return $this->redirectToRoute("cart_show");
        }

        // Si un user est connecté, on récupère son panier
        $cartItems = $this->cartService->getCartItems();
        if (count($cartItems) === 0) {
            $this->addFlash("warning", "Aucun produit dans le panier");
            return $this->redirectToRoute("cart_show");
        }

        // Et on crée la commande
        /** @var Purchase */
        $purchase = $form->getData();
        $this->purchasePersister->createPurchase($purchase, $cartItems);

        return $this->redirectToRoute("purchase_payment_form", [
            'id' => $purchase->getId()
        ]);
    }
}
