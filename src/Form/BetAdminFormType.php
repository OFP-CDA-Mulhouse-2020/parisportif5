<?php

namespace App\Form;

use App\Entity\Team;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BetAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('winner', EntityType::class, [
                'mapped' => false,
                'required' => false,
                'label' => "Paris vainqueur",
                'class' => Team::class,
                'choices' => $options['run_teams'],
                'choice_label' => 'name',
                'expanded' => true
            ])
            ->add('valid', SubmitType::class, [
                'label' => "Valider"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'run_teams' => new ArrayCollection()
        ]);
    }
}
