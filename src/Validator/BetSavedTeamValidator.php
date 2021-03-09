<?php

namespace App\Validator;

use App\Entity\BetSaved;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BetSavedTeamValidator
{
    public static function validate(BetSaved $betSavedObject, ExecutionContextInterface $context): void
    {
        $notAutorizedEmptyValues = false;
        if (
            (
                empty($betSavedObject->getTeamName())
                || empty($betSavedObject->getTeamCountry())
            )
            &&
            (
                !empty($betSavedObject->getTeamName())
                || !empty($betSavedObject->getTeamCountry())
            )
        ) {
            $notAutorizedEmptyValues = true;
        }
        if ($notAutorizedEmptyValues === true) {
            $context->buildViolation('Not all team properties are empty or filled.')
                ->atPath('teamName')
                ->addViolation()
            ;
        }
    }
}
