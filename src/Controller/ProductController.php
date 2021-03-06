<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use Doctrine\ORM\EntityManager;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Route("{slug}", name="product_category", priority=-1)
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
     * @Route("/{category_slug}/{slug}", name="product_show", priority="-1")
     */
    public function show($slug, Request $request, EventDispatcherInterface $dispatcher): Response
    {

        $product = $this->productRepository->findOneBy([
            "slug" => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas');
        }

        $productViewEvent = new ProductViewEvent($product);
        $dispatcher->dispatch($productViewEvent, 'product.view');

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

        if ($form->isSubmitted() && $form->isValid()) {
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
     * @Route("/admin/product/edit/index", name="product_index")
     */
    public function productIndex()
    {
        return $this->render("product/edit_product_index.html.twig", [
            "products" => $this->productRepository->findAll()
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit(int $id, Request $request, SluggerInterface $slugger, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        // $person = [
        //     'nom' => "hubert",
        //     'prenom' => "gérard",
        //     'adresse' => [
        //         "rue" => 'de la gardette',
        //         'code postal' => 13690,
        //         'ville' => 'Graveson'
        //     ]
        // ];

        // $constraints = new Collection([
        //     'nom' => [
        //         new NotBlank(['message' => 'Le nom ne doit pas être vide']),
        //         new Length(
        //             ['min' => 3, 'minMessage' => 'le nom doit faire au moins {{ limit }} caracteres']
        //         )
        //     ],
        //     'prenom' => new NotBlank(['message' => 'Le prénom doit être renseigné']),
        //     'adresse' => new Collection([
        //         'rue' => new NotBlank(['message' => "l'adresse doit être renseignée"]),
        //         'code postal' => new LessThan(['value' => 98000, 'message' => "le code postale doit être inférieur à {{ compared_value }}"]),
        //         'ville' => new NotBlank(['message' => 'la ville doit être renseignée'])
        //     ])
        // ]);

        // $validation = $validator->validate($person, $constraints);

        // dd($validation);

        // $age = 25;
        // $validation = $validator->validate($age, [
        //     new GreaterThanOrEqual([
        //         'value' => 35,
        //         'message' => "Soit honnête... On sait que tu as plus de {{ compared_value }} ans mais que tu as saisi {{ value }} ans"
        //     ])
        // ]);

        $product = $this->productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute("product_show", [
                "category_slug" => $product->getCategory()->getSlug(),
                "slug" => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/{id}/delete", name="product_delete")
     */
    public function delete($id, EntityManagerInterface $em)
    {
        $product = $this->productRepository->find($id);

        if ($product === null) {
            $this->addFlash('error', 'Ce produit n\'existe pas');
            return $this->redirectToRoute('product_index');
        }

        $em->remove($product);
        $em->flush();
        $this->addFlash('success', 'Le produit ' . $id . ' a bien été supprimé');

        return $this->redirectToRoute('product_index');
    }
}
