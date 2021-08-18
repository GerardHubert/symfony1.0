<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;

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
    public function create(FormFactoryInterface $factory): Response
    {
        $builder = $factory->createBuilder();
        $builder->add('name', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'placeholder' => "Tapez le nom du nouveau produit"
            ],
            'label' => 'Nom du produit',
        ])
            ->add('shortDescription', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Tapez une courte, mais parlante, description du produit"
                ],
                'label' => "Description du produit"
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Tapez le prix en €"
                ],
                'label' => "Prix du produit"
            ])
            ->add('category', EntityType::class, [
                'label' => "Catégorie",
                'attr' => [
                    'class' => 'form-control',
                ],
                'placeholder' => "-- Selectionnez une catégorie --",
                'class' => Category::class,
                'choice_label' => "name"
            ]);

        $form = $builder->getForm();

        $formView = $form->createView();

        return $this->render("product/create.html.twig", [
            'formView' => $formView
        ]);
    }
}
