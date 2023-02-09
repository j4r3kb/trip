<?php

declare(strict_types=1);

namespace App\BusinessTrip\Application\Command;

use DateTimeInterface;

class AddBusinessTripCommand
{
    public function __construct(
        public readonly string $employeeId,
        public readonly DateTimeInterface $startDate,
        public readonly DateTimeInterface $endDate,
        public readonly string $countryAlpha2
    )
    {
    }
}
