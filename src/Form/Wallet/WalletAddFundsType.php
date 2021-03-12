<?php

namespace App\Form\Wallet;

use Symfony\Component\Form\AbstractType;
use App\Form\Model\WalletAddFundsFormModel;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class WalletAddFundsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amountAdd', MoneyType::class, [
                'required' => true,
                'label' => "Montant",
                'divisor' => 100,
                'currency' => $options['data']->getCurrency() ?? false,
                'invalid_message' => "Veuillez saisir un montant avec des chiffres."
            ])
            ->add('addFunds', SubmitType::class, [
                'label' => "Créditer votre porte-monnaie"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WalletAddFundsFormModel::class
        ]);
    }
}
