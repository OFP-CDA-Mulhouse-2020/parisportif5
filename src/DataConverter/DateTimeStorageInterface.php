<?php

declare(strict_types=1);

namespace App\DataConverter;

interface DateTimeStorageInterface
{
    public function convertedToStoreDateTime(\DateTimeInterface $datetime): \DateTimeImmutable;
    public function setStoredTimezone(\DateTimeImmutable $datetimeImmutable): \DateTimeImmutable;
}
