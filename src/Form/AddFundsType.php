<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Wallet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddFundsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('amount', NumberType::class, [
            'required' => true,
            'label' => "Montant à ajouter : ",
            'invalid_message' => "Veuillez saisir le nombre du montant à ajouter au portefeuille.",
            'input' => 'number'
            ])
        ->add('Ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Wallet::class,
            // 'validation_groups' => ['addfunds']
        ]);
    }
}
