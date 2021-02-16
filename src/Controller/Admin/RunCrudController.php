<?php

namespace App\Controller\Admin;

use App\Entity\Run;
use App\Form\Bet\AdminManyBetResultFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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

    public function configureActions(Actions $actions): Actions
    {
        $viewResultEditing = Action::new('viewResultEditing', 'Bet Result Editing', 'fas fa-award')
            /*->displayIf(static function (Run $run) {
                return !$run->canBet();
            })*/
            ->linkToRoute('admin_run_bet_result', function (Run $run): array {
                return [
                    'runId' => $run->getId()
                ];
            });
        return $actions
            ->add(Crud::PAGE_INDEX, $viewResultEditing)
            ->add(Crud::PAGE_EDIT, $viewResultEditing)
            ->add(Crud::PAGE_DETAIL, $viewResultEditing)
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
        yield Field\AssociationField::new('competition', 'Competition')->onlyWhenUpdating();
        yield Field\CollectionField::new('teams', 'Teams')->hideOnIndex();
        /*$options = $this->getFormTypeOptions();
        yield Field\FormField::addPanel('Bet Categories on Run')->onlyWhenUpdating()
            ->setFormType(AdminManyBetResultFormType::class)
            ->setFormTypeOptions($options);*/
        //yield Field\FormField::new('betCategories', 'Bet Categories')->onlyWhenUpdating();
    }

    /*private function getFormTypeOptions(): array
    {
        $options = [
            'target' => null,
            'data_list' => []
        ];
        return $options;
    }*/
}
