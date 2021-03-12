<?php

namespace App\Form\Wallet;

use Symfony\Component\Form\AbstractType;
use App\Form\Model\WalletRetireFundsFormModel;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class WalletRetireFundsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('walletFunds', HiddenType::class, [
                'mapped' => false,
                'property_path' => 'walletAmount'
            ])
            ->add('amountRetire', MoneyType::class, [
                'required' => true,
                'label' => "Montant",
                'divisor' => 100,
                'currency' => $options['data']->getCurrency() ?? false,
                'invalid_message' => "Veuillez saisir un montant avec des chiffres."
            ])
            ->add('addFunds', SubmitType::class, [
                'label' => "Débiter votre porte-monnaie"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WalletRetireFundsFormModel::class
        ]);
    }
}
