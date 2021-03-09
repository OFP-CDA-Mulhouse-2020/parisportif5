<?php

namespace App\Validator;

use App\Entity\BetSaved;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BetSavedMemberValidator
{
    public static function validate(BetSaved $betSavedObject, ExecutionContextInterface $context): void
    {
        $notAutorizedEmptyValues = false;
        if (
            (
                empty($betSavedObject->getMemberCountry())
                || empty($betSavedObject->getMemberFirstName())
                || empty($betSavedObject->getMemberLastName())
            )
            &&
            (
                !empty($betSavedObject->getMemberCountry())
                || !empty($betSavedObject->getMemberFirstName())
                || !empty($betSavedObject->getMemberLastName())
            )
        ) {
            $notAutorizedEmptyValues = true;
        }
        if ($notAutorizedEmptyValues === true) {
            $context->buildViolation('Not all member properties are empty or filled.')
                ->atPath('memberLastName')
                ->addViolation()
            ;
        }
    }
}
