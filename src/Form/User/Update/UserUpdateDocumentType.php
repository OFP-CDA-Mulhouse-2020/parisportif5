<?php

namespace App\Form\User\Update;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UserUpdateDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                $builder->create('identityDocuments', FormType::class, [
                    'inherit_data' => true,
                    'label' => "Justificatif(s) d'identité"
                ])
                ->add('identityDocumentFile', FileType::class, [
                    'label' => "Chercher le document (carte d'identité, passeport, permis de conduire, carte vitale)",
                    'mapped' => false,
                    'required' => false
                ])
                ->add('identityDocumentAdd', SubmitType::class, [
                    'label' => "Ajouter"
                ])
            )
            ->add(
                $builder->create('residenceProofs', FormType::class, [
                    'inherit_data' => true,
                    'label' => "Justificatif(s) de domicile"
                ])
                ->add('residenceProofFile', FileType::class, [
                    'label' => "Chercher le document (facture d'énergie, avis d'imposition)",
                    'mapped' => false,
                    'required' => false
                ])
                ->add('residenceProofAdd', SubmitType::class, [
                    'label' => "Ajouter"
                ])
            )
        ;
    }
}
