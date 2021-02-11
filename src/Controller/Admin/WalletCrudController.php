<?php

namespace App\Controller\Admin;

use App\Entity\Billing;
use App\Entity\Wallet;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class WalletCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Wallet::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Wallet')
            ->setEntityLabelInPlural('Wallets')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Wallet Details');
        yield Field\IdField::new('id', 'ID')->hideOnForm();
        yield Field\AssociationField::new('user', 'User');
        yield Field\MoneyField::new('amount', 'Amount')->setCurrency(Billing::DEFAULT_CURRENCY_CODE)->setNumDecimals(2)->setStoredAsCents(true);
    }
}
