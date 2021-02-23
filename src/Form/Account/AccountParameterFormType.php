<?php

namespace App\Form\Account;

use App\Form\Model\UserFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as FieldType;

class AccountParameterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                $builder->create('acceptNewsletters', FieldType\FormType::class, [
                    'inherit_data' => true,
                    'label' => "Newsletter(s)"
                ])
                ->add('newsletters', FieldType\CheckboxType::class, [
                    'label' => "J'accepte de recevoir les offres promotionnelles de notre site",
                    'required' => false
                ])
            )
            ->add(
                $builder->create('options', FieldType\FormType::class, [
                    'inherit_data' => true,
                    'label' => "Option(s)"
                ])
                ->add('timeZoneSelected', FieldType\TimezoneType::class, [
                    'required' => true,
                    'label' => "Sélection du fuseau horaire",
                    'trim' => true,
                    'invalid_message' => "Veuillez sélectionner un fuseau horaire.",
                    'placeholder' => 'Choisissez un fuseau horaire'
                ])
            )
            ->add('modifyUserParameters', FieldType\SubmitType::class, [
                'label' => "Modifier paramètre(s)"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserFormModel::class,
            'validation_groups' => ['parameter']
        ]);
    }
}
