<?php

namespace App\Form\Bet;

use App\Service\OddsStorageDataConverter;
use App\Entity\Bet;
use App\Entity\Team;
use App\Entity\Member;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $oddsStorageDataConverter = $options['converter'];
        $builder
            ->add($options['property_mapped'], EntityType::class, [
                'required' => $options['target_required'],
                'label' => $options['category_label'],
                'class' => $options['class_name'],
                'choices' => $options['run_targets'],
                'choice_label' => function ($target) use ($oddsStorageDataConverter) {
                    $label = '';
                    if ($target instanceof Team) {
                        $label = $target->getName() ?? '';
                    }
                    if ($target instanceof Member) {
                        $team = $target->getTeam();
                        $teamName = ($team->getName() ?? '');
                        $label = ($target->getLastName() ?? '') . ' ' . ($target->getFirstName() ?? '') . ' - ' . $teamName;
                    }
                    $odds = $target->getOdds() ?? 0;
                    $odds = $oddsStorageDataConverter->convertToOddsMultiplier($odds);
                    $label = $odds . ' - ' . $label;
                    return $label;
                },
                'expanded' => $options['target_expanded'],
                'placeholder' => $options['target_placeholder']
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
            'data_class' => Bet::class,
            'run_targets' => [],
            'converter' => new OddsStorageDataConverter(),
            'target_required' => true,
            'target_expanded' => true,
            'target_placeholder' => false,
            'property_mapped' => "",
            'category_label' => "",
            'class_name' => ""
        ]);
    }
}
