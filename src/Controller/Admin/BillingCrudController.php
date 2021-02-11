<?php

namespace App\Controller\Admin;

use App\Entity\Billing;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BillingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Billing::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Billing')
            ->setEntityLabelInPlural('Billings')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Billing Details');
        yield Field\IdField::new('id', 'ID')->onlyOnIndex();
        yield Field\AssociationField::new('user', 'User');
        yield Field\TextField::new('lastName', 'Last Name');
        yield Field\TextField::new('firstName', 'First Name');
        yield Field\TextField::new('address', 'Address');
        yield Field\TextField::new('city', 'City');
        yield Field\TextField::new('postcode', 'Postcode');
        yield Field\CountryField::new('country', 'Country');
        yield Field\TextField::new('designation', 'Designation');
        yield Field\IntegerField::new('orderNumber', 'Order Number');
        yield Field\IntegerField::new('invoiceNumber', 'Invoice Number');
        yield Field\MoneyField::new('amount', 'Amount')->setCurrency(Billing::DEFAULT_CURRENCY_CODE)->setNumDecimals(2)->setStoredAsCents(true);
        yield Field\PercentField::new('commissionRate', 'Commission Rate')->setNumDecimals(2)->setStoredAsFractional(true)->setSymbol('%');
        $operationTypeValues = Billing::OPERATION_TYPES;
        $operationTypeValues = array_map(function ($value) {
            return ucfirst($value);
        }, $operationTypeValues);
        $operationTypeChoices = array_combine($operationTypeValues, Billing::OPERATION_TYPES);
        yield Field\ChoiceField::new('operationType', 'Operation Type')->allowMultipleChoices(false)->setChoices($operationTypeChoices)->renderExpanded(true);
        yield Field\DateTimeField::new('issueDate', 'Issue Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\DateTimeField::new('deliveryDate', 'Delivery Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
    }
}
