<?php

namespace App\Form\Bet;

use App\DataConverter\OddsStorageDataConverter;
use App\Entity\Bet;
use App\Entity\Team;
use Doctrine\Common\Collections\ArrayCollection;
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
        //ChoiceList::label();
        //'choice_label' => 'name',
        //dd($options);
        $oddsStorageDataConverter = $options['converter'];
        $builder
            ->add('team', EntityType::class, [
                'required' => $options['team_required'],
                'label' => "Vainqueur",
                'class' => Team::class,
                'choices' => $options['run_teams'],
                'choice_label' => function ($team) use ($oddsStorageDataConverter) {
                    $label = $team->getName() ?? '';
                    $odds = $team->getOdds() ?? 0;
                    $odds = $oddsStorageDataConverter->convertToOddsMultiplier($odds);
                    $label .= ' - ' . $odds;
                    return $label;
                },
                'expanded' => $options['team_expanded'],
                'placeholder' => $options['team_placeholder']
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
            'run_teams' => new ArrayCollection(),
            'converter' => new OddsStorageDataConverter(),
            'team_required' => true,
            'team_expanded' => true,
            'team_placeholder' => ""
        ]);
    }
}
