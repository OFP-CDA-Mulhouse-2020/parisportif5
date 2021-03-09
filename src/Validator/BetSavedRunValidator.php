<?php

namespace App\Validator;

use App\Entity\BetSaved;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BetSavedRunValidator
{
    public static function validate(BetSaved $betSavedObject, ExecutionContextInterface $context): void
    {
        $notAutorizedEmptyValues = false;
        if (
            (
                empty($betSavedObject->getRunEvent())
                || empty($betSavedObject->getRunName())
                || empty($betSavedObject->getRunStartDate())
            )
            &&
            (
                !empty($betSavedObject->getRunEvent())
                || !empty($betSavedObject->getRunName())
                || !empty($betSavedObject->getRunStartDate())
            )
        ) {
            $notAutorizedEmptyValues = true;
        }
        if ($notAutorizedEmptyValues === true) {
            $context->buildViolation('Not all run properties are empty or filled.')
                ->atPath('runName')
                ->addViolation()
            ;
        }
    }
}
