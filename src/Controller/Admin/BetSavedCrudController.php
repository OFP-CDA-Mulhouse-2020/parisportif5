<?php

namespace App\Controller\Admin;

use App\Entity\BetSaved;
use App\Entity\Billing;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class BetSavedCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BetSaved::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Bet saved')
            ->setEntityLabelInPlural('Bets saved')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Bet saved Details');
        yield Field\IdField::new('id', 'ID')->hideOnForm();
        yield Field\AssociationField::new('user', 'User');
        yield Field\TextField::new('betCategoryName', 'Category Name');
        yield Field\TextField::new('designation', 'Designation');
        yield Field\MoneyField::new('amount', 'Amount')->setCurrency(Billing::DEFAULT_CURRENCY_CODE)->setNumDecimals(2)->setStoredAsCents(true);
        yield Field\MoneyField::new('gains', 'Gains')->setCurrency(Billing::DEFAULT_CURRENCY_CODE)->setNumDecimals(2)->setStoredAsCents(true);
        yield Field\NumberField::new('odds', 'Odds')->setNumDecimals(2)->setStoredAsString(true);
        $winningChoices = [
            'Pending' => '',
            'Yes' => 1,
            'No' => 0
        ];
        yield Field\ChoiceField::new('isWinning', 'Winning')->allowMultipleChoices(false)->setChoices($winningChoices)->renderExpanded(true);
        yield Field\DateTimeField::new('betDate', 'Creation Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\TextField::new('competitionName', 'Competition Name');
        yield Field\DateTimeField::new('competitionStartDate', 'Competition Start Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\CountryField::new('competitionCountry', 'Competition Country')->showFlag(false);
        ;
        yield Field\TextField::new('competitionSportName', 'Competition Sport Name');
        yield Field\CountryField::new('competitionSportCountry', 'Competition Sport Country')->showFlag(false);
        ;
        yield Field\TextField::new('runName', 'Run Name');
        yield Field\TextField::new('runEvent', 'Run Event');
        yield Field\DateTimeField::new('runStartDate', 'Run Start Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\TextField::new('teamName', 'Team Name');
        yield Field\CountryField::new('teamCountry', 'Team Country')->showFlag(false);
        ;
        yield Field\TextField::new('memberLastName', 'Member Last Name');
        yield Field\TextField::new('memberFirstName', 'Member First Name');
        yield Field\CountryField::new('memberCountry', 'Member Country')->showFlag(false);
    }
}
