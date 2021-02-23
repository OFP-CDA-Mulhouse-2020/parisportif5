<?php

namespace App\Form\Account;

use App\Form\Model\UserFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as FieldType;

class AccountDocumentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('certifiesAccurate', FieldType\CheckboxType::class, [
                'label' => "Je certifie sur l'honneur que les données fournies sont exactes",
                'required' => true
            ])
            ->add(
                $builder->create('identityDocumentFile', FieldType\FormType::class, [
                    'inherit_data' => true,
                    'label' => "Justificatif d'identité"
                ])
                ->add('identityDocumentFileName', FieldType\TextType::class, [
                    'label' => "Fichier actuel",
                    'disabled' => true,
                    'required' => false
                ])
                ->add('userIdentityDocumentReplace', FieldType\SubmitType::class, [
                    'label' => "Remplacer le document d'identité",
                    'validation_groups' => ['identity_document']
                ])
                ->add('identityDocument', FieldType\FileType::class, [
                    'label' => "Chercher le document (carte d'identité, passeport, permis de conduire, carte vitale)",
                    'required' => false
                ])
            )
            ->add(
                $builder->create('residenceProofFile', FieldType\FormType::class, [
                    'inherit_data' => true,
                    'label' => "Justificatif de domicile"
                ])
                ->add('residenceProofFileName', FieldType\TextType::class, [
                    'label' => "Fichier actuel",
                    'disabled' => true,
                    'required' => false
                ])
                ->add('userResidenceProofReplace', FieldType\SubmitType::class, [
                    'label' => "Remplacer le document de résidence",
                    'validation_groups' => ['residence_document']
                ])
                ->add('residenceProof', FieldType\FileType::class, [
                    'label' => "Chercher le document (facture d'énergie, avis d'imposition)",
                    'required' => false
                ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserFormModel::class
        ]);
    }
}
