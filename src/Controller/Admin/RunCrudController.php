<?php

namespace App\Controller\Admin;

use App\Entity\Run;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class RunCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Run::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Run')
            ->setEntityLabelInPlural('Runs')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Run Details');
        yield Field\IdField::new('id', 'ID')->hideOnForm();
        yield Field\TextField::new('name', 'Name');
        yield Field\TextField::new('event', 'Event');
        yield Field\DateTimeField::new('startDate', 'Start Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\AssociationField::new('location', 'Location');
        yield Field\AssociationField::new('competition', 'Competition');
        yield Field\CollectionField::new('teams', 'Teams')->hideOnIndex();
    }
}
