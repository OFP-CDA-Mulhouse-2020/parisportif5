<?php

namespace App\Form\Bet;

use App\Form\Model\AdminBetResultFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminBetResultFormType extends AbstractType
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
            ->add('setwinning', SubmitType::class, [
                'label' => "Valider"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminBetResultFormModel::class
        ]);
    }
}
