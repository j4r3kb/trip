<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\ValueObject;

use App\BusinessTrip\Domain\Exception\BusinessTripStartDateGreaterOrEqualEndDateException;
use Carbon\CarbonImmutable;
use DateTimeInterface;

final class BusinessTripDuration
{
    private function __construct(
        private readonly DateTimeInterface $startDate,
        private readonly DateTimeInterface $endDate
    )
    {
    }

    public static function create(DateTimeInterface $startDate, DateTimeInterface $endDate): self
    {
        if ($startDate >= $endDate) {
            throw BusinessTripStartDateGreaterOrEqualEndDateException::create($startDate, $endDate);
        }

        return new self($startDate, $endDate);
    }

    public function startDate(): CarbonImmutable
    {
        return CarbonImmutable::create($this->startDate);
    }

    public function endDate(): CarbonImmutable
    {
        return CarbonImmutable::create($this->endDate);
    }

    public function overlapsWith(BusinessTripDuration $other): bool
    {
        return ($this->startDate < $other->endDate) && ($other->startDate < $this->endDate);
    }
}
