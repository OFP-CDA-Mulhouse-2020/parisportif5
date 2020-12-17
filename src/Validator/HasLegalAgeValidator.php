<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class HasLegalAgeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /* @var $constraint \App\Validator\HasLegalAge */
        if (!$constraint instanceof HasLegalAge) {
            throw new UnexpectedTypeException($constraint, HasLegalAge::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof \DateTimeImmutable) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new \UnexpectedValueException("Cette valeur $value n'est pas un objet DateTimeImmutable");
        }

        $minAge = User::MIN_AGE_FOR_BETTING;
        if ($this->hasLegalAge($value, $minAge)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ integer }}', (string)$minAge)
                ->addViolation();
        }
    }

    private function hasLegalAge(\DateTimeImmutable $value, int $minAge): bool
    {
        $legalAgeDate = \DateTime::createFromImmutable($value);
        $timeZoneString = 'UTC';
        $timeZoneObject = new \DateTimeZone($timeZoneString);
        $legalAgeDate = $legalAgeDate->setTimezone($timeZoneObject);
        $currentDate = new \DateTime('now', $timeZoneObject);
        $currentDate = $currentDate->setTime(23, 59, 59, 999999);
        $legalAgeDate = $legalAgeDate->setTime(23, 59, 60);
        $legalAgeDate->add(new \DateInterval('P' . $minAge . 'Y'));
        return ($legalAgeDate > $currentDate);
    }
}
