<?php

namespace App\Controller\Admin;

use App\Entity\Bet;
use App\Entity\Billing;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class BetCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bet::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Bet')
            ->setEntityLabelInPlural('Bets')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Bet Details');
        yield Field\IdField::new('id', 'ID')->hideOnForm();
        yield Field\AssociationField::new('user', 'User');
        yield Field\AssociationField::new('betCategory', 'Category');
        yield Field\TextField::new('designation', 'Designation');
        yield Field\MoneyField::new('amount', 'Amount')->setCurrency(Billing::DEFAULT_CURRENCY_CODE)->setNumDecimals(2)->setStoredAsCents(true);
        yield Field\NumberField::new('odds', 'Odds')->setNumDecimals(2)->setStoredAsString(true);
        $winningChoices = [
            'Pending' => '',
            'Yes' => 1,
            'No' => 0
        ];
        yield Field\ChoiceField::new('isWinning', 'Winning')->allowMultipleChoices(false)->setChoices($winningChoices)->renderExpanded(true);
        yield Field\DateTimeField::new('betDate', 'Creation Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\AssociationField::new('competition', 'Competition Target');
        /*$closureA = function (?string $value) {
            if (is_null($value) === true) {
                return 'none';
            }
            return $value;
        };*/
        yield Field\AssociationField::new('run', 'Run Target');
        yield Field\AssociationField::new('team', 'Team Select');
        yield Field\AssociationField::new('teamMember', 'Member Select');
        /*$test = Field\AssociationField::new('target', 'Target');
        $closure = function ($value) use ($test) {
            if ($value instanceof Run) {
                $test->setProperty('run');
            }
            if ($value instanceof Competition) {
                $test->setProperty('competition');
            }
        };
        yield $test->setQueryBuilder($closure);
        yield Field\AssociationField::new('select', 'Select');*/
    }
}
