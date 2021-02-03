<?php

namespace App\Validator;

use DateTimeInterface;
use App\Repository\BetRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueBetValidator extends ConstraintValidator
{
    private BetRepository $betRepository;
    private Security $security;

    public function __construct(BetRepository $betRepository, Security $security)
    {
        $this->betRepository = $betRepository;
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint): void
    {
        /* @var $constraint \App\Validator\UniqueBet */
        if (!$constraint instanceof UniqueBet) {
            throw new UnexpectedTypeException($constraint, UniqueBet::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if ($value instanceof DateTimeInterface) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'DateTimeInterface');
        }

        $user = $this->security->getUser();
        $existingBet = $this->betRepository->findOneBy([
            'betBate' => $value,
            'user' => $user
        ]);

        if ($existingBet !== null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', "6")
                ->addViolation();
        }
    }
}
