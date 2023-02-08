<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Exception;

use DateTimeInterface;
use DomainException;

class BusinessTripStartDateGreaterOrEqualEndDateException extends DomainException
{
    public static function create(DateTimeInterface $startDate, DateTimeInterface $endDate): static
    {
        return new static(
            sprintf(
                'Start date %s is greater or equal to end date %s',
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s')
            )
        );

    }
}
