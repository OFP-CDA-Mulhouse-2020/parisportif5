<?php

namespace App\Form;

use App\Entity\Bet;
use App\Entity\Team;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('team', EntityType::class, [
                'required' => false,
                'label' => "Vainqueur",
                'class' => Team::class,
                'choices' => $options['run_teams'],
                'choice_label' => 'name',
                'expanded' => true
            ])
            ->add('amount', IntegerType::class, [
                'required' => true,
                'label' => "Montant",
                'invalid_message' => "Veuillez saisir un chiffre entier."
            ])
            ->add('betting', SubmitType::class, [
                'label' => "Parier"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bet::class,
            'run_teams' => new ArrayCollection()
        ]);
    }
}
