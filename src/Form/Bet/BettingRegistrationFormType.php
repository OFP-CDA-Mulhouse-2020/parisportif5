<?php

namespace App\Form\Bet;

use Symfony\Component\Form\AbstractType;
use App\Form\Model\BettingRegistrationFormModel;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BettingRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd($options['data']);
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

        /*
        ->add('result', EntityType::class, [
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
        */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BettingRegistrationFormModel::class,
            'result_choices' => [],
            'category_label' => "",
            'bool_null_select' => false
        ]);
    }
}
