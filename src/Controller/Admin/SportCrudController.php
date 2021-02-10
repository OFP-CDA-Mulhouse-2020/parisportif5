<?php

namespace App\Controller\Admin;

use App\Entity\Sport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class SportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sport::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Sport')
            ->setEntityLabelInPlural('Sports')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Sport Details');
        yield Field\IdField::new('id', 'ID')->onlyOnIndex();
        yield Field\TextField::new('name', 'Name');
        yield Field\CountryField::new('country', 'Country');
        $runTypeValues = Sport::RUN_TYPES;
        $runTypeValues = array_map(function ($value) {
            return ucfirst($value);
        }, $runTypeValues);
        $runTypeChoices = array_combine($runTypeValues, Sport::RUN_TYPES);
        yield Field\ChoiceField::new('runType', 'Run Type')->allowMultipleChoices(false)->setChoices($runTypeChoices)->renderExpanded(true);
        yield Field\BooleanField::new('individualType', 'Individual Type')->renderAsSwitch(false);
        yield Field\BooleanField::new('collectiveType', 'Collective Type')->renderAsSwitch(false);
        yield Field\IntegerField::new('minTeamsByRun', 'Minimum Number of Teams by Run');
        $closureA = function (?int $value) {
            if (is_null($value) === true) {
                return 'no limit';
            }
            return $value;
        };
        yield Field\IntegerField::new('maxTeamsByRun', 'Maximum Number of Teams by Run')->formatValue($closureA);
        yield Field\IntegerField::new('minMembersByTeam', 'Minimum Number of Members by Run');
        $closureB = function (?int $value) {
            if (is_null($value) === true) {
                return 'no limit';
            }
            return $value;
        };
        yield Field\IntegerField::new('maxMembersByTeam', 'Maximum Number of Members by Run')->formatValue($closureB);
    }
}
