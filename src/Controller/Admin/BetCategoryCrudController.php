<?php

namespace App\Controller\Admin;

use App\Entity\BetCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class BetCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BetCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Bet Category')
            ->setEntityLabelInPlural('Bet Categories')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('Bet Category Details');
        yield Field\IdField::new('id', 'ID')->hideOnForm();
        yield Field\TextField::new('name', 'Name');
        yield Field\TextareaField::new('description', 'Description');
        $targetValues = BetCategory::TARGET_TYPES;
        $targetValues = array_map(function ($value) {
            return ucfirst($value);
        }, $targetValues);
        $targetChoices = array_combine($targetValues, BetCategory::TARGET_TYPES);
        yield Field\ChoiceField::new('target', 'Target')->allowMultipleChoices(false)->setChoices($targetChoices)->renderExpanded(true);
        yield Field\BooleanField::new('allowDraw', 'Allow Draw')->renderAsSwitch(false);
        yield Field\BooleanField::new('onCompetition', 'On Competition')->renderAsSwitch(false);
    }
}
