<?php

declare(strict_types=1);

namespace App\BusinessTrip\Application\Command;

class AddBusinessTripCommand
{
    public function __construct(
        public readonly string $employeeId,
        public readonly string $tripStartDate,
        public readonly string $tripEndDate,
        public readonly string $countryAlpha2
    )
    {
    }
}
