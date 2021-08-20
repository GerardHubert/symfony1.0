<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => "Tapez le nom du nouveau produit"
                ],
                'label' => 'Nom du produit',
            ])
            ->add('shortDescription', TextareaType::class, [
                'attr' => [
                    'placeholder' => "Tapez une courte, mais parlante, description du produit"
                ],
                'label' => "Description du produit"
            ])
            ->add('mainPicture', UrlType::class, [
                'attr' => [
                    'placeholder' => "Tapez l'URL de l'image du produit"
                ],
                'label' => 'URL de l\'image'
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'placeholder' => "Tapez le prix en €"
                ],
                'label' => "Prix du produit"
            ])
            ->add('category', EntityType::class, [
                'label' => "Catégorie",
                'placeholder' => "-- Selectionnez une catégorie --",
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                }
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
