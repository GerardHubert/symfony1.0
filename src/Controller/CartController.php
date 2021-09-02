<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CartController extends AbstractController
{
    private $productRepository;
    private $session;
    private $cartService;

    public function __construct(ProductRepository $productRepository, SessionInterface $session, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->session = $session;
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show()
    {
        $cart = $this->cartService->getCartItems();
        $totalCart = $this->cartService->getTotal();

        return $this->render('cart/cart.html.twig', [
            'cart' => $cart,
            'totalCart' => $totalCart
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id"="\d+"})
     */
    public function add($id, Request $request)
    {
        // On cherche si le produit demandé existe dans la BDD
        $product = $this->productRepository->find($id);
        if ($product === null) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        $this->cartService->add($id);

        if ($request->query->has("backToCart")) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('product_show', [
            'product' => $product,
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id"="\d+"})
     */
    public function delete($id)
    {
        $session = $this->session->get('cart');

        if (!array_key_exists($id, $session)) {
            return;
        }

        $this->cartService->remove($id);

        return $this->redirectToRoute("cart_show");
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id"="\d+"})
     */
    public function decrement($id)
    {
        $cart =  $this->session->get('cart');

        if (!array_key_exists($id, $cart)) {
            return;
        }

        $this->cartService->minus($id);

        return $this->redirectToRoute("cart_show");
    }
}
