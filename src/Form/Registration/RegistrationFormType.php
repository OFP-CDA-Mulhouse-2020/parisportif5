<?php

namespace App\Form\Registration;

use App\Entity\User;
use App\Form\Model\UserFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as FieldType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', FieldType\RepeatedType::class, [
                'type' => FieldType\EmailType::class,
                'required' => true,
                'trim' => true,
                'invalid_message' => "Veuillez saisir une adresse email valide.",
                'first_options'  => ['label' => "Email"],
                'second_options' => ['label' => "Confirmer l'email"]
            ])
            ->add('newPassword', FieldType\RepeatedType::class, [
                'type' => FieldType\PasswordType::class,
                'required' => true,
                'trim' => false,
                'invalid_message' => "Veuillez saisir un mot de passe valide.",
                'first_options'  => ['label' => "Mot de passe"],
                'second_options' => ['label' => "Confirmer le mot de passe"]
            ])
            ->add('birthDate', FieldType\BirthdayType::class, [
                'required' => true,
                'label' => "Date de naissance",
                'invalid_message' => "Veuillez sélectionner une date de naissance.",
                'input' => 'datetime_immutable',
                'model_timezone' => User::STORED_TIME_ZONE,
                'widget' => 'single_text'
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
                'label' => "Code postal de facturation",
                'trim' => true,
                'invalid_message' => "Veuillez saisir un code postal de facturation."
            ])
            ->add('billingCountry', FieldType\CountryType::class, [
                'required' => true,
                'label' => "Pays de facturation",
                'invalid_message' => "Veuillez saisir un pays de facturation.",
                'data' => 'FR'
            ])
            ->add('timeZoneSelected', FieldType\TimezoneType::class, [
                'required' => true,
                'label' => "Sélection du fuseau horaire",
                'trim' => true,
                'invalid_message' => "Veuillez sélectionner un fuseau horaire valide.",
                'placeholder' => 'Choisissez un fuseau horaire'
            ])
            ->add('acceptTerms', FieldType\CheckboxType::class, [
                'required' => true,
                'label' => "J'accepte les conditions générales d'utilisation"
            ])
            ->add('newsletters', FieldType\CheckboxType::class, [
                'label' => "J'accepte de recevoir les offres promotionnelles de notre site",
                'required' => false
            ])
            ->add('identityDocument', FieldType\FileType::class, [
                'label' => "Document d'identité",
                'help' => "Documents acceptés : carte d'identité, passeport, permis de conduire, carte vitale.",
                'required' => true
            ])
            ->add('residenceProof', FieldType\FileType::class, [
                'label' => "Justificatif de domicile",
                'help' => "Documents acceptés : facture d'énergie, avis d'imposition.",
                'required' => true
            ])
            ->add('certifiesAccurate', FieldType\CheckboxType::class, [
                'label' => "Je certifie sur l'honneur que les données fournies sont exactes",
                'required' => true
            ])
            ->add('registerNewUser', FieldType\SubmitType::class, [
                'label' => "S'inscrire"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserFormModel::class,
            'validation_groups' => ['registration']
        ]);
    }
}
