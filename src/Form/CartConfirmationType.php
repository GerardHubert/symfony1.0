<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CartConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => "Votre nom et prénom",
                'attr' => [
                    'placeholder' => "Votre nom ici"
                ]
            ])
            ->add('address', TextareaType::class, [
                'label' => "Votre adresse",
                'attr' => [
                    'placeholder' => "Adresse de livraison ici"
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => "Code Postal",
                'attr' => [
                    'placeholder' => "Code postal de la ville de livraison"
                ]
            ])
            ->add('city', TextType::class, [
                'label' => "Ville",
                'attr' => [
                    'placeholder' => "Ville de livraison"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            "data_class" => Purchase::class
        ]);
    }
}
