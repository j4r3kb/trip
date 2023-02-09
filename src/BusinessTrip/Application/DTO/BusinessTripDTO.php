<?php

declare(strict_types=1);

namespace App\BusinessTrip\Application\DTO;

use DateTimeInterface;

class BusinessTripDTO
{
    public function __construct(
        public DateTimeInterface $startDate,
        public DateTimeInterface $endDate,
        public string $countryCode
    )
    {
    }
}
