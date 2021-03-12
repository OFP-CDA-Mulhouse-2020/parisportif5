<?php

namespace App\Form\Account;

use App\Form\Model\UserFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as FieldType;

class AccountUpdatePersonalDataFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civility', FieldType\ChoiceType::class, [
                'required' => false,
                'label' => "Civilité",
                'placeholder' => 'Aucune',
                'invalid_message' => "La valeur n'est pas valide.",
                'choices' => [
                    'Madame' => 'Madame',
                    'Monsieur' => 'Monsieur'
                ]
            ])
            ->add('firstName', FieldType\TextType::class, [
                'required' => true,
                'label' => "Prénom",
                'trim' => true,
                'invalid_message' => "Veuillez saisir un prénom."
            ])
            ->add('lastName', FieldType\TextType::class, [
                'required' => true,
                'label' => "Nom",
                'trim' => true,
                'invalid_message' => "Veuillez saisir un nom de famille."
            ])
            ->add('billingAddress', FieldType\TextType::class, [
                'required' => true,
                'label' => "Adresse de facturation",
                'trim' => true,
                'invalid_message' => "Veuillez saisir une adresse de facturation."
            ])
            ->add('billingCity', FieldType\TextType::class, [
                'required' => true,
                'label' => "Ville de facturation",
                'trim' => true,
                'invalid_message' => "Veuillez saisir une ville de facturation."
            ])
            ->add('billingPostcode', FieldType\TextType::class, [
                'required' => true,
                'label' => "Code Postal de facturation",
                'trim' => true,
                'invalid_message' => "Veuillez saisir un code postal de facturation."
            ])
            ->add('billingCountry', FieldType\CountryType::class, [
                'required' => true,
                'label' => "Pays de facturation",
                'invalid_message' => "Veuillez saisir un pays de facturation."
            ])
            ->add('modifyUserProfile', FieldType\SubmitType::class, [
                'label' => "Valider vos modifications"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserFormModel::class,
            'validation_groups' => ['profile']
        ]);
    }
}
