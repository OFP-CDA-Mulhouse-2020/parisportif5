<?php

namespace App\Form\Account;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountParameterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                $builder->create('newsletters', FormType::class, [
                    'inherit_data' => true,
                    'label' => "Newsletter(s)"
                ])
                ->add('acceptNewsletters', CheckboxType::class, [
                    'label' => "J'accepte de recevoir les offres promotionnelles de notre site",
                    'mapped' => false,
                    'required' => false
                ])
            )
            ->add(
                $builder->create('options', FormType::class, [
                    'inherit_data' => true,
                    'label' => "Option(s)"
                ])
                ->add('timeZoneSelected', TimezoneType::class, [
                    'required' => true,
                    'label' => "Sélection du fuseau horaire",
                    'trim' => true,
                    'invalid_message' => "Veuillez sélectionner un fuseau horaire.",
                    'placeholder' => 'Choisissez un fuseau horaire'
                ])
            )
            ->add('modify', SubmitType::class, [
                'label' => "Modifier"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['parameter']
        ]);
    }
}
