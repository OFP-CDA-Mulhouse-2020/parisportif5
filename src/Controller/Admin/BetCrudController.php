<?php

namespace App\Controller\Admin;

use App\Entity\Bet;
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
        return [
            Field\IdField::new('id', 'ID'),
            Field\TextField::new('designation', 'Designation'),
            Field\MoneyField::new('amount', 'Amount')->setCurrency('EUR')->setNumDecimals(2)->setStoredAsCents(true),
            Field\NumberField::new('odds', 'Odds')->setNumDecimals(2)->setStoredAsString(true),
            Field\BooleanField::new('isWinning', 'Winning')->renderAsSwitch(false),
            Field\DateTimeField::new('betDate', 'Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss'),
        ];
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
            IntegerField::new('stock'),
            Field\PercentField::new('odds', 'Odds')->setNumDecimals(2)->setStoredAsFractional(true)->setSymbol('%'),
        ];
    }
    */
}
