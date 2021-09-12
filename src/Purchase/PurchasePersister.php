<?php

declare(strict_types=1);

namespace App\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePersister extends AbstractController
{
    private $cartService;
    private $em;

    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function createPurchase(Purchase $purchase, $cartItems)
    {
        // On lie la commande avec l'utilisateur connecté (on set le user dans la purchase)
        $purchase->setUser($this->getUser());
        //on set la date et le total de commande dans l'entité avec un évènement doctrine
        // ->setPurchasedAt(new DateTime('now'))
        // ->setTotal($this->cartService->getTotal());
        $this->em->persist($purchase);

        // on récupère chaque produit du panier grace a CartService->getCartItems a qui on dit de retourner un tableau d'items (@return CartItem[])
        // avec la relation, on a aussi accès aux products
        // Pour chaque produit du panier on crée un nouveau purchaseItem qu'on ajoute dans la purchase globale
        foreach ($cartItems as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->getQuantity())
                ->setTotal($cartItem->getTotal());

            $this->em->persist($purchaseItem);
        }

        $this->em->flush();
    }
}
