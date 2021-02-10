<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field as Field;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users')
            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addPanel('User Details');
        yield Field\IdField::new('id', 'ID')->onlyOnIndex();
        yield Field\EmailField::new('email', 'Email');
        yield Field\ArrayField::new('roles', 'Roles');
        yield Field\TextField::new('civility', 'Civility');
        yield Field\TextField::new('lastName', 'Last Name');
        yield Field\TextField::new('firstName', 'First Name');
        yield Field\TextField::new('billingAddress', 'Billing Address');
        yield Field\TextField::new('billingCity', 'Billing City');
        yield Field\TextField::new('billingPostcode', 'Billing Postcode');
        yield Field\CountryField::new('billingCountry', 'Billing Country');
        yield Field\DateField::new('birthDate', 'Birth Date')->setFormat('dd/MM/yyyy');
        yield Field\TimezoneField::new('timeZoneSelected', 'Timezone Selected');
        yield Field\BooleanField::new('deletedStatus', 'Deleted')->renderAsSwitch(false);
        yield Field\DateTimeField::new('deletedDate', 'Deleted Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\BooleanField::new('suspendedStatus', 'Suspended')->renderAsSwitch(false);
        yield Field\DateTimeField::new('suspendedDate', 'Suspended Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\BooleanField::new('activatedStatus', 'Activated')->renderAsSwitch(false);
        yield Field\DateTimeField::new('activatedDate', 'Activated Date')->setTimezone('Europe/Paris')->setFormat('dd/MM/yyyy HH:mm:ss');
        yield Field\BooleanField::new('isVerified', 'Verified')->renderAsSwitch(false);
        yield Field\BooleanField::new('newsletters', 'Newsletters')->renderAsSwitch(false);
        yield Field\TextField::new('identityDocument', 'Identity Document');
        yield Field\TextField::new('residenceProof', 'Residence Proof');
        yield Field\AssociationField::new('language', 'Language');
        yield Field\AssociationField::new('wallet', 'Wallet');
        yield Field\CollectionField::new('onGoingBets', 'On Going Bets')->hideOnIndex();
    }
}
