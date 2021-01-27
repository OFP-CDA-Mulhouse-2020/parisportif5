<?php

declare(strict_types=1);

namespace App\Service;

use App\DataConverter\DateTimeStorageInterface;

final class DateTimeStorageDataConverter implements DateTimeStorageInterface
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
        $this->setStoredTimezone($datetimeToStored);
        return $datetimeToStored;
    }

    public function setStoredTimezone(\DateTimeImmutable $datetimeImmutable): \DateTimeImmutable
    {
        $timezone = $datetimeImmutable->getTimezone();
        if (empty($timezone)) {
            $timezone = new \DateTimeZone(self::STORED_TIME_ZONE);
            $datetimeImmutable = $datetimeImmutable->setTimezone($timezone);
        }
        if ($timezone->getName() !== self::STORED_TIME_ZONE) {
            $timezone = new \DateTimeZone(self::STORED_TIME_ZONE);
            $datetimeImmutable = $datetimeImmutable->setTimezone($timezone);
        }
        return $datetimeImmutable;
    }
}
