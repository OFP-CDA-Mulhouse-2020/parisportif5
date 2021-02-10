<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class TeamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Team::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Team')
            ->setEntityLabelInPlural('Teams')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Team Details');
        yield Field\IdField::new('id', 'ID')->onlyOnIndex();
        yield Field\TextField::new('name', 'Name');
        yield Field\CountryField::new('country', 'Country');
        yield Field\NumberField::new('odds', 'Odds')->setNumDecimals(2)->setStoredAsString(true);
        yield Field\AssociationField::new('sport', 'Sport');
        yield Field\CollectionField::new('members', 'Members')->hideOnIndex();
    }
}
