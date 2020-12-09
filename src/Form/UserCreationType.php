<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /*$currentLanguage = \Locale::getDefault();
        $currentCountry = strtoupper(preg_replace('/^[a-z]+\_/i', '', $currentLanguage));
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $currentCountry);
        $timezone = count($timezones) == 1 ? $timezones[0] : 'UTC';*/
        /*$minAge = User::MIN_AGE_FOR_BETTING;
        $currentYear = (int)(new \DateTime('now', new \DateTimeZone('UTC')))->format('Y');*/
        $builder
            ->add('civility', ChoiceType::class, [
                'required' => true,
                'label' => "Civilité",
                'invalid_message' => "Veuillez sélectionner une civilité",
                'placeholder' => 'Choisissez une civilité',
                'choices' => [
                    'Monsieur' => 'Monsieur',
                    'Madame' => 'Madame'
                ]
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => "Prénom",
                'invalid_message' => "Veuillez saisir un prénom"
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => "Nom",
                'invalid_message' => "Veuillez saisir un nom de famille"
            ])
            ->add('billingAddress', TextType::class, [
                'required' => true,
                'label' => "Adresse de facturation",
                'invalid_message' => "Veuillez saisir une adresse de facturation"
            ])
            ->add('billingCity', TextType::class, [
                'required' => true,
                'label' => "Ville de facturation",
                'invalid_message' => "Veuillez saisir une ville de facturation"
            ])
            ->add('billingPostcode', TextType::class, [
                'required' => true,
                'label' => "Code Postal de facturation",
                'invalid_message' => "Veuillez saisir un code postal de facturation"
            ])
            ->add('billingCountry', CountryType::class, [
                'required' => true,
                'label' => "Pays de facturation",
                'invalid_message' => "Veuillez saisir un pays de facturation",
                'data' => 'FR'
            ])
            ->add('birthDate', BirthdayType::class, [
                'required' => true,
                'label' => "Date de naissance",
                'invalid_message' => "Veuillez sélectionner une date de naissance",
                'input' => 'datetime',
                'model_timezone' => User::DATABASE_TIME_ZONE,
                'widget' => 'single_text'
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'required' => true,
                'invalid_message' => "Veuillez saisir une adresse email valide",
                'first_options'  => ['label' => "Email"],
                'second_options' => ['label' => "Confirmer l'email"]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => "Veuillez saisir un mot de passe valide",
                'first_options'  => ['label' => "Mot de passe"],
                'second_options' => ['label' => "Confirmer le mot de passe"]
            ])
            ->add('timeZoneSelected', TimezoneType::class, [
                'label' => "Fuseau horaire",
                'data' => 'Europe/Paris'
            ])
            ->add('save', SubmitType::class, [
                'label' => "Valider"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
