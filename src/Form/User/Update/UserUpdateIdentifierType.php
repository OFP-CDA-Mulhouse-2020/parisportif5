<?php

namespace App\Form\User\Update;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserUpdateIdentifierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'required' => true,
                'trim' => true,
                'invalid_message' => "Veuillez saisir une nouvelle adresse email valide.",
                'first_options'  => ['label' => "Nouvelle adresse email"],
                'second_options' => ['label' => "Confirmer la nouvelle adresse email"]
            ])
            ->add('modify', SubmitType::class, [
                'label' => "Modifier"
            ])
        ;

        /*->add('oldEmail', EmailType::class, [
                'required' => false,
                'trim' => true,
                'mapped' => false,
                'invalid_message' => "Veuillez saisir votre ancienne adresse email.",
                'label' => "Ancienne adresse email",
                'data' => "",
                'disabled' => true
                'constraints' => [
                    new IdenticalTo([
                        'value' => "",
                        'message' => "Votre ancienne adresse email n'est pas reconnue."
                    ])
                ]
            ])*/
        /*->add('emailValidation', NumberType::class, [
                'required' => true,
                'trim' => true,
                'html5' => false,
                'scale' => 0,
                'mapped' => false,
                'invalid_message' => "Veuillez saisir le code reçu.",
                'label' => "Saisisez le code reçu sur votre nouvelle adresse email",
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'max' => 6,
                        'exactMessage' => "Le code saisi ne contient pas {{ limit }} chiffres."
                    ]),
                    new Positive([
                        'message' => "Le code saisi n'est pas positif."
                    ])
                ]
            ])*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['identifier_update']
        ]);
    }
}
