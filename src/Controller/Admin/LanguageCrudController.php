<?php

namespace App\Controller\Admin;

use App\Entity\Language;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class LanguageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Language::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Language')
            ->setEntityLabelInPlural('Languages')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Language Details');
        yield Field\IdField::new('id', 'ID')->hideOnForm();
        yield Field\TextField::new('name', 'Name');
        yield Field\CountryField::new('country', 'Country');
        yield Field\LocaleField::new('code', 'Code')->showCode(true)->showName(false);
        yield Field\TextField::new('dateFormat', 'Date Format');
        yield Field\TextField::new('timeFormat', 'Time Format');
        yield Field\TimezoneField::new('capitalTimeZone', 'Capital Timezone');
    }
}
