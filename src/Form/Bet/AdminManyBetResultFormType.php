<?php

namespace App\Form\Bet;

use App\Entity\Run;
use App\Entity\BetCategory;
use App\Entity\Competition;
use App\Form\Model\BetChoiceGenerator;
use Symfony\Component\Form\AbstractType;
use App\DataConverter\OddsStorageInterface;
use App\Form\Model\AdminBetResultFormModel;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminManyBetResultFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $target = $options['target'];
        $betCategories = $options['data_list'];
        foreach ($betCategories as $betCategory) {
            $betCategoryId = $betCategory->getId();
            $adminBetResultFormModel = null;
            if ($target instanceof Run) {
                $competition = $target->getCompetition();
                $adminBetResultFormModel = $this->createAdminBetResultFormModel($betCategory, $competition, $target, null, true);
            }
            if ($target instanceof Competition) {
                $adminBetResultFormModel = $this->createAdminBetResultFormModel($betCategory, $target, null, null, true);
            }
            $builder
                ->add('betCategoryId_' . $betCategoryId, AdminBetResultFormType::class, [
                    'data' => $adminBetResultFormModel
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'target' => null,
            'data_list' => []
        ]);
    }

    protected function createAdminBetResultFormModel(
        BetCategory $betCategory,
        Competition $competition,
        ?Run $run = null,
        ?OddsStorageInterface $oddsStorageDataConverter = null,
        bool $admin = false
    ): AdminBetResultFormModel {
        $betChoiceGenerator = new BetChoiceGenerator(
            $betCategory,
            $competition,
            $run,
            $oddsStorageDataConverter,
            $admin
        );
        return new AdminBetResultFormModel(
            $betChoiceGenerator->getChoices(),
            $betChoiceGenerator->getCategoryLabel(),
            $betChoiceGenerator->getCategoryId()
        );
    }
}
