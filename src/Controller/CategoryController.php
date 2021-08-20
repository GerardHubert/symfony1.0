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
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class);
        $formView = $form->createView();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $category = $form->getData();
            $category->setSlug(
                strtolower(
                    $slugger->slug($category->getName())
                )
            );

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/create.html.twig', [
            'formView' => $formView,
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit(int $id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = $categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $formView = $form->createView();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $category->setSlug(
                strtolower(
                    $slugger->slug($category->getName())
                )
            );

            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
