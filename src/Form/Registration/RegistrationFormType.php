<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class RegistrationFormType extends AbstractType
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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'trim' => false,
                'invalid_message' => "Veuillez saisir un mot de passe valide.",
                'first_options'  => ['label' => "Mot de passe"],
                'second_options' => ['label' => "Confirmer le mot de passe"]
            ])
            ->add('birthDate', BirthdayType::class, [
                'required' => true,
                'label' => "Date de naissance",
                'invalid_message' => "Veuillez sélectionner une date de naissance.",
                'input' => 'datetime_immutable',
                'model_timezone' => User::STORED_TIME_ZONE,
                'widget' => 'single_text'
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
            ->add('timeZoneSelected', TimezoneType::class, [
                'required' => true,
                'label' => "Sélection du fuseau horaire",
                'trim' => true,
                'invalid_message' => "Veuillez sélectionner un fuseau horaire.",
                'placeholder' => 'Choisissez un fuseau horaire'
            ])
            ->add('acceptTerms', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
                'label' => "J'accepte les conditions générales d'utilisation",
                'constraints' => [
                    new IsTrue([
                        'message' => "Vous devez accepter les conditions générales d'utilisation pour vous inscrire.",
                    ])
                ]
            ])
            ->add('newsletters', CheckboxType::class, [
                'label' => "J'accepte de recevoir les offres promotionnelles de notre site",
                'required' => false
            ])
            ->add('identityDocument', FileType::class, [
                'label' => "Document d'identité (carte ID, passeport, permis de conduire ...)",
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => "Fichier obligatoire pour l'inscription !",
                    ]),
                    new File([
                        'maxSize' => "1M",
                        'mimeTypes' => ["application/pdf", "image/jpeg", "image/png"],
                        'mimeTypesMessage' => "Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté.",
                        'disallowEmptyMessage' => "Le fichier spécifier est vide.",
                        'maxSizeMessage' => "La taille maximale autorisée est de {{ limit }} {{ suffix }}."
                    ])
                ]
            ])
            ->add('residenceProof', FileType::class, [
                'label' => "Justificatif de domicile (Facture, avis d'imposition ...)",
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => "Fichier obligatoire pour l'inscription !",
                    ]),
                    new File([
                        'maxSize' => "1M",
                        'mimeTypes' => ["application/pdf", "image/jpeg", "image/png"],
                        'mimeTypesMessage' => "Seule les fichiers au format PDF, PNG, JPG et JPEG sont accepté.",
                        'disallowEmptyMessage' => "Le fichier spécifier est vide.",
                        'maxSizeMessage' => "La taille maximale autorisée est de {{ limit }} {{ suffix }}."
                    ])
                ]
            ])
            ->add('certifiesAccurate', CheckboxType::class, [
                'label' => "Je certifie sur l'honneur que les données fournies sont exactes",
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => "Vous devez certifier sur l'honneur que les données fournies sont exactes.",
                    ])
                ]
            ])
            ->add('register', SubmitType::class, [
                'label' => "S'inscrire"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['registration']
        ]);
    }
}
