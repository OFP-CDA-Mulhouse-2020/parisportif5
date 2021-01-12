<?php

namespace App\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UserProfileDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                $builder->create('identityDocuments', FormType::class, array('inherit_data' => true))
                ->add('identityDocumentFile', FileType::class, [
                    'label' => "Document d'identité (carte ID, passeport, permis de conduire ...)",
                    'mapped' => false,
                    'required' => false
                ])
                ->add('identityDocumentAdd', SubmitType::class, [
                    'label' => "Ajouter"
                ])
            )
            ->add(
                $builder->create('residenceProofs', FormType::class, array('inherit_data' => true))
                ->add('residenceProofFile', FileType::class, [
                    'label' => "Justificatif de domicile (Facture, avis d'imposition ...)",
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
