<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Exception;

use DateTimeInterface;
use DomainException;

class BusinessTripDurationOverlapException extends DomainException
{
    public static function create(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        DateTimeInterface $otherStartDate,
        DateTimeInterface $otherEndDate
    ): static
    {
        return new static(
            sprintf(
                'Business trip duration %s - %s overlaps with %s - %s',
                $startDate,
                $endDate,
                $otherStartDate,
                $otherEndDate
            ),
            400
        );
    }
}
