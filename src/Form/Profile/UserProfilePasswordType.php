<?php

namespace App\Form\Profile;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserProfilePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', HiddenType::class, [
                'required' => true,
                'trim' => true,
                'invalid_message' => "Le prénom n'est défini."
            ])
            ->add('lastName', HiddenType::class, [
                'required' => true,
                'trim' => true,
                'invalid_message' => "Le nom de famille n'est défini."
            ])
            ->add('oldPassword', PasswordType::class, [
                'required' => true,
                'trim' => false,
                'mapped' => false,
                'invalid_message' => "Veuillez saisir votre ancien mot de passe.",
                'label' => "Ancien mot de passe",
                'constraints' => [
                    new UserPassword([
                        'message' => "Votre ancien mot de passe n'est pas reconnu."
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
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
            'validation_groups' => ['password_update']
        ]);
    }
}
