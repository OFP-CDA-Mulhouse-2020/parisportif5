<?php

namespace App\Validator;

use App\Entity\BetSaved;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BetSavedValidator
{
    public static function validate(BetSaved $betSavedObject, ExecutionContextInterface $context): void
    {
        // deafult values
        $propertyAttached = 'runName';
        $violationMessage = 'Not all run, team, member properties are empty or filled.';
        $errorClassName = '';
        // check datas
        $runError = false;
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
            $runError = true;
            $errorClassName = 'Run';
        }
        $teamError = false;
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
            $teamError = true;
            $errorClassName = 'Team';
        }
        $memberError = false;
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
            $memberError = true;
            $errorClassName = 'Member';
        }
        // set test
        $notAutorizedEmptyValues = false;
        if (
            $memberError === true
            || $teamError === true
            || $runError === true
        ) {
            $notAutorizedEmptyValues = true;
            // set text
            if (
                $memberError === true
                && $teamError === true
            ) {
                $violationMessage = 'Not all team, member properties are empty or filled.';
            }
            if (
                $memberError === true
                && $runError === true
            ) {
                $violationMessage = 'Not all run, member properties are empty or filled.';
            }
            if (
                $runError === true
                && $teamError === true
            ) {
                $violationMessage = 'Not all run, team properties are empty or filled.';
            }
            switch ($errorClassName) {
                case 'Team':
                    $propertyAttached = 'teamName';
                    $violationMessage = 'Not all team properties are empty or filled.';
                    break;
                case 'Member':
                    $propertyAttached = 'memberLastName';
                    $violationMessage = 'Not all member properties are empty or filled.';
                    break;
                case 'Run':
                    $propertyAttached = 'runName';
                    $violationMessage = 'Not all run properties are empty or filled.';
                    break;
            }
        }
        if ($notAutorizedEmptyValues === true) {
            $context->buildViolation($violationMessage)
                ->atPath($propertyAttached)
                ->addViolation()
            ;
        }
    }
}
