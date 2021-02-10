<?php

namespace App\Controller\Admin;

use App\Entity\MemberStatus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class MemberStatusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MemberStatus::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Member Status')
            ->setEntityLabelInPlural('Member Status')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Member Status Details');
        yield Field\IdField::new('id', 'ID')->onlyOnIndex();
        yield Field\TextField::new('name', 'Name');
    }
}
