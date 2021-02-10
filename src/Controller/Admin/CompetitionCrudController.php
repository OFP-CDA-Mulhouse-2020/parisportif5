<?php

namespace App\Controller\Admin;

use App\Entity\Competition;
use Doctrine\Common\Collections\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class CompetitionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Competition::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Competititon')
            ->setEntityLabelInPlural('Competititons')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Competititon Details');
        yield Field\IdField::new('id', 'ID')->hideOnForm();
        yield Field\TextField::new('name', 'Name');
        yield Field\DateTimeField::new('startDate', 'Start Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\CountryField::new('country', 'Country');
        yield Field\IntegerField::new('minRuns', 'Minimum Number of Runs');
        $closure = function (?int $value) {
            if (is_null($value) === true) {
                return 'no limit';
            }
            return $value;
        };
        yield Field\IntegerField::new('maxRuns', 'Maximum Number of Runs')->formatValue($closure);
        yield Field\AssociationField::new('sport', 'Sport');
        yield Field\CollectionField::new('betCategories', 'Bet Categories')->hideOnIndex();
        yield Field\CollectionField::new('runs', 'Runs')->hideOnIndex();
    }
}
