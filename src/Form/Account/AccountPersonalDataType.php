<?php

namespace App\Form\Account;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountPersonalDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'required' => false,
                'label' => "Civilité",
                //'invalid_message' => "Veuillez sélectionner une civilité",
                'placeholder' => 'Aucune', //'Choisissez une civilité',
                'choices' => [
                    'Madame' => 'Madame',
                    'Monsieur' => 'Monsieur'
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => "Email",
                'disabled' => true
            ])
            ->add('modifyEmail', ButtonType::class, [
                'label' => "Modifier l'adresse email"
            ])
            ->add('zeroPassword', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'label' => "Mot de passe",
                'empty_data' => "00000000",
                'disabled' => true
            ])
            ->add('modifyPassword', ButtonType::class, [
                'label' => "Modifier le mot de passe"
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => "Prénom",
                'trim' => true,
                'invalid_message' => "Veuillez saisir un prénom."
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => "Nom",
                'trim' => true,
                'invalid_message' => "Veuillez saisir un nom de famille."
            ])
            ->add('billingAddress', TextType::class, [
                'required' => true,
                'label' => "Adresse de facturation",
                'trim' => true,
                'invalid_message' => "Veuillez saisir une adresse de facturation."
            ])
            ->add('billingCity', TextType::class, [
                'required' => true,
                'label' => "Ville de facturation",
                'trim' => true,
                'invalid_message' => "Veuillez saisir une ville de facturation."
            ])
            ->add('billingPostcode', TextType::class, [
                'required' => true,
                'label' => "Code Postal de facturation",
                'trim' => true,
                'invalid_message' => "Veuillez saisir un code postal de facturation."
            ])
            ->add('billingCountry', CountryType::class, [
                'required' => true,
                'label' => "Pays de facturation",
                'invalid_message' => "Veuillez saisir un pays de facturation.",
                'data' => 'FR'
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
            'validation_groups' => ['profile']
        ]);
    }
}
