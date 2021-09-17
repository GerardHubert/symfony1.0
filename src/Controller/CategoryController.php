<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setSlug(
                strtolower(
                    $slugger->slug($category->getName())
                )
            );

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie' . $category->getName() . 'a bien été créée');
            return $this->redirectToRoute('category_index');
        }

        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView,
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit(int $id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, Security $security): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, "Vous n'avez pas le droit d'accéder à cette ressource");

        // $user = $security->getUser();

        // if ($user === null) {
        //     return $this->redirectToRoute('security_login');
        // }
        // if (in_array('ROLE_ADMIN', $user->getRoles()) === false) {
        //     throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à accéder à cette ressource");
        // }

        $category = $categoryRepository->find($id);

        if ($category === null) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }

        // $user = $this->getUser(); //$security->getUser()
        // Le seecuroty controller explore tous les voter en envoyant l'attribut et le subject qu'on lui pass. ici 'CAN_EDIT et $category
        // $this->denyAccessUnlessGranted('CAN_EDIT', $category, "T'as pas le droit");

        // if ($user === null) {
        //     return $this->redirectToRoute("security_login");
        // }

        // if ($user !== $category->getOwner()) {
        //     throw new AccessDeniedHttpException("Vous n'êtes pas le propriétaire de cette catégorie");
        // }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(
                strtolower(
                    $slugger->slug($category->getName())
                )
            );

            $em->flush();

            $this->addFlash('success', 'La catégorie' . $category->getName() . ' a bien été modifiée');

            return $this->redirectToRoute('category_index');
        }

        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/category/index", name="category_index")
     */
    public function index(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/category_index.html.twig', [
            "categories" => $categories
        ]);
    }

    /**
     * @Route("/admin/category/{id}/delete", name="category_delete")
     */
    public function delete($id, CategoryRepository $categoryRepository, EntityManagerInterface $em)
    {
        $category = $categoryRepository->find($id);

        if ($category === null) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }

        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'La catégorie  a bien été supprimée');

        return $this->redirectToRoute("category_index");
    }
}
