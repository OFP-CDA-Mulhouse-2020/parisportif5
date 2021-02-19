<?php

namespace App\Form\Account;

use App\Form\Model\UserFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as FieldType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AccountUpdatePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', FieldType\PasswordType::class, [
                'required' => true,
                'trim' => false,
                'invalid_message' => "Veuillez saisir votre ancien mot de passe.",
                'label' => "Ancien mot de passe"
            ])
            ->add('plainPassword', FieldType\RepeatedType::class, [
                'type' => FieldType\PasswordType::class,
                'required' => true,
                'trim' => false,
                'invalid_message' => "Veuillez saisir un nouveau mot de passe valide.",
                'first_options'  => ['label' => "Nouveau mot de passe"],
                'second_options' => ['label' => "Confirmer le nouveau mot de passe"]
            ])
            ->add('modify', FieldType\SubmitType::class, [
                'label' => "Modifier"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserFormModel::class,
            'validation_groups' => ['password_update']
        ]);
    }
}
