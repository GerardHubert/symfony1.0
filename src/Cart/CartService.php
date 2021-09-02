<?php

declare(strict_types=1);

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartService
{
    private $session;
    private $productRepository;
    private $flashBag;

    public function __construct(SessionInterface $session, ProductRepository $productRepository, FlashBagInterface $flashBag)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->flashBag = $flashBag;
    }

    public function add(int $id): array
    {
        // On recherche s'il y a  un panier dans la session
        // Et s'il n'y en a pas, le panier est un tableau vide
        $cart = $this->session->get('cart', []);

        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // Si tout va bien, on met à jour le panier et on redirige vers la page du produit ajouté
        $this->session->set('cart', $cart);

        // On enregistre un message flash dans le bag flash (récupéré dans le template twig)
        // ou $this->addFlash() = raccourci de l'abstract controller
        $this->flashBag->set('success', "Le produit a bien été ajouté au panier");

        return $cart;
    }

    public function getCartItems(): array
    {
        $cart = [];

        // A partir du panier en session, on récupère les entités produits et la quantité, qu'on stocke sous ls forme d'un tableau ayant pour clés "product" et "quantity"
        foreach ($this->session->get('cart', []) as $id => $quantity) {
            $product = $this->productRepository->find($id);
            // $cart[] = [
            //     "product" => $product,
            //     "quantity" => $quantity,
            //     "totalProduct" => $product->getPrice() * $quantity
            // ];
            $cart[] = new CartItem($product, $quantity);
        }

        return $cart;
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->session->get('cart') as $id => $quantity) {
            $product = $this->productRepository->find($id);
            $total += $product->getPrice() * $quantity;
        }

        return $total;
    }

    public function remove(int $id)
    {
        $cart = $this->session->get('cart');

        unset($cart[$id]);
        $this->session->set('cart', $cart);

        $this->flashBag->add('success', "Le produit a bien été supprimé de votre panier");
    }

    public function minus(int $id)
    {
        $cart = $this->session->get('cart', []);
        dump($cart);
        if (array_key_exists($id, $cart)) {
            if ($cart[$id] === 1) {
                $this->remove($id);
                return;
            } else {
                $cart[$id]--;
                $this->session->set('cart', $cart);
                $this->flashBag->add('success', "La quantité a bien été diminué de 1");
            }
        }
    }
}
