<?php

namespace App\Form\Account;

use App\Form\Model\UserFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as FieldType;

class AccountUpdateIdentifierFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', FieldType\PasswordType::class, [
                'required' => true,
                'trim' => false,
                'invalid_message' => "Veuillez saisir votre mot de passe pour pouvoir modifier votre adresse email.",
                'label' => "Mot de passe actuel"
            ])
            ->add('newEmail', FieldType\RepeatedType::class, [
                'type' => FieldType\EmailType::class,
                'required' => true,
                'trim' => true,
                'invalid_message' => "Veuillez saisir votre nouvelle adresse email de façon identique aux deux endroits spécifiés.",
                'first_options'  => ['label' => "Nouvelle adresse email"],
                'second_options' => ['label' => "Confirmer la nouvelle adresse email"]
            ])
            ->add('modifyUserIdentifier', FieldType\SubmitType::class, [
                'label' => "Valider la modification"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserFormModel::class,
            'validation_groups' => ['identifier_update']
        ]);
    }
}
