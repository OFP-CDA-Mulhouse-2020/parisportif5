<?php

namespace App\Form\Bet;

use App\Entity\Competition;
use App\Entity\Run;
use App\Form\Model\AdminBetResultFormModel;
use Symfony\Component\Form\AbstractType;
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
            $adminBetResultFormModel = new AdminBetResultFormModel();
            if ($target instanceof Run) {
                $adminBetResultFormModel->initializeWithRun($betCategory, $target);
            }
            if ($target instanceof Competition) {
                $adminBetResultFormModel->initializeWithCompetition($betCategory, $target);
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
}
