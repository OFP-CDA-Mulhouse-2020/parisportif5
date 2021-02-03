<?php

namespace App\Form\Bet;

use Symfony\Component\Form\AbstractType;
use App\Form\Model\BettingRegistrationFormModel;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BettingRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('result', ChoiceType::class, [
                'required' => true,
                'label' => $options['data']->getCategoryLabel(),
                'choices' => $options['data']->getChoices(),
                'choice_label' => 'label',
                'choice_value' => 'id',
                'expanded' => true,
                'placeholder' => false
            ])
            ->add('amount', MoneyType::class, [
                'required' => true,
                'label' => "Montant",
                'divisor' => 100,
                'currency' => false,
                'invalid_message' => "Veuillez saisir un montant avec des chiffres."
            ])
            ->add('betting', SubmitType::class, [
                'label' => "Parier"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BettingRegistrationFormModel::class
        ]);
    }
}
