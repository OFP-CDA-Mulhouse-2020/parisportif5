<?php

namespace App\Controller\Admin;

use App\Entity\MemberRole;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class MemberRoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MemberRole::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Member Role')
            ->setEntityLabelInPlural('Member Roles')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Member Role Details');
        yield Field\IdField::new('id', 'ID')->onlyOnIndex();
        yield Field\TextField::new('name', 'Name');
    }
}
