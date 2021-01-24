<?php

namespace App\Form\Bet;

use App\Entity\Member;
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
                'required' => $options['target_required'],
                'label' => $options['category_label'],
                'class' => $options['class_name'],
                'choices' => $options['run_targets'],
                'choice_label' => function ($target) {
                    $label = '';
                    if ($target instanceof Team) {
                        $label = $target->getName() ?? '';
                    }
                    if ($target instanceof Member) {
                        $team = $target->getTeam();
                        $teamName = ($team->getName() ?? '');
                        $label = ($target->getLastName() ?? '') . ' ' . ($target->getFirstName() ?? '') . ' - ' . $teamName;
                    }
                    return $label;
                },
                'expanded' => $options['target_expanded'],
                'placeholder' => $options['target_placeholder']
            ])
            ->add('valid', SubmitType::class, [
                'label' => "Valider"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'run_targets' => new ArrayCollection(),
            'target_required' => true,
            'target_expanded' => true,
            'target_placeholder' => "",
            'category_label' => "",
            'class_name' => ""
        ]);
    }
}
