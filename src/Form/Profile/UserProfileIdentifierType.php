<?php

namespace App\Form\Profile;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileIdentifierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'required' => true,
                'trim' => true,
                'invalid_message' => "Veuillez saisir une adresse email valide.",
                'first_options'  => ['label' => "Email"],
                'second_options' => ['label' => "Confirmer l'email"]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'trim' => false,
                'invalid_message' => "Veuillez saisir un nouveau mot de passe valide.",
                'first_options'  => ['label' => "Nouveau mot de passe"],
                'second_options' => ['label' => "Confirmer le nouveau mot de passe"]
            ])
            ->add('modify', SubmitType::class, [
                'label' => "Modifier"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['identification']
        ]);
    }
}
