<?php

declare(strict_types=1);

namespace App\Entity;

use App\DataConverter\DateTimeStorageInterface;

abstract class AbstractEntity implements DateTimeStorageInterface
{
    public const STORED_TIME_ZONE = "UTC";

    public function convertedToStoreDateTime(\DateTimeInterface $datetime): \DateTimeImmutable
    {
        $datetimeToStored = null;
        if ($datetime instanceof \DateTimeImmutable) {
            $datetimeToStored = $datetime;
        }
        if ($datetime instanceof \DateTime) {
            $datetimeToStored = \DateTimeImmutable::createFromMutable($datetime);
        }
        $this->setStoredTimeZone($datetimeToStored);
        return $datetimeToStored;
    }

    public function setStoredTimeZone(\DateTimeImmutable $datetimeImmutable): \DateTimeImmutable
    {
        $timeZone = $datetimeImmutable->getTimezone();
        if (empty($timeZone)) {
            $timeZone = new \DateTimeZone(self::STORED_TIME_ZONE);
            $datetimeImmutable = $datetimeImmutable->setTimezone($timeZone);
        }
        if ($timeZone->getName() !== self::STORED_TIME_ZONE) {
            $timeZone = new \DateTimeZone(self::STORED_TIME_ZONE);
            $datetimeImmutable = $datetimeImmutable->setTimezone($timeZone);
        }
        return $datetimeImmutable;
    }
}
