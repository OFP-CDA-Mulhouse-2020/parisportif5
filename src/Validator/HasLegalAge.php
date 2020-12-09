<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HasLegalAge extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Vous n\'avez pas l\'âge requis de {{ integer }} ans pour vous inscrire.';
}
