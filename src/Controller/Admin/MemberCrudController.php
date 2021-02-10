<?php

namespace App\Controller\Admin;

use App\Entity\Member;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class MemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Member::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Member')
            ->setEntityLabelInPlural('Members')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Member Details');
        yield Field\IdField::new('id', 'ID')->onlyOnIndex();
        yield Field\TextField::new('lastName', 'Last Name');
        yield Field\TextField::new('firstName', 'First Name');
        yield Field\CountryField::new('country', 'Country');
        yield Field\NumberField::new('odds', 'Odds')->setNumDecimals(2)->setStoredAsString(true);
        yield Field\AssociationField::new('team', 'Team');
        yield Field\AssociationField::new('memberStatus', 'Member Role');
        yield Field\AssociationField::new('memberRole', 'Member Status');
    }
}
