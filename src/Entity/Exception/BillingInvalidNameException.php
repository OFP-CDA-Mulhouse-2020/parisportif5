<?php

declare(strict_types=1);

namespace App\Entity\Exception;

class BillingInvalidNameException extends \LogicException
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
