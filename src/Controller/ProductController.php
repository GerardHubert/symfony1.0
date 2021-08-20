<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use Doctrine\ORM\EntityManager;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $productRepository;
    private $categoryRepository;

    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("{slug}", name="product_category")
     */
    public function category($slug): Response
    {

        $category = $this->categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        };

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug): Response
    {
        $product = $this->productRepository->findOneBy([
            "slug" => $slug
        ]);

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        // $builder = $factory->createBuilder(ProductType::class);
        $form = $this->createForm(ProductType::class);

        // $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            $em->persist($product);
            $em->flush($product);

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        };

        $formView = $form->createView();

        return $this->render("product/create.html.twig", [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit")
     */
    public function edit(int $id, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $product = $this->productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);
        $formView = $form->createView();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();
            return $this->redirectToRoute("product_show", [
                "category_slug" => $product->getCategory()->getSlug(),
                "slug" => $product->getSlug()
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }
}
