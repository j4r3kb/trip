<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Repository;

use App\BusinessTrip\Domain\Entity\BusinessTrip;
use App\BusinessTrip\Domain\ValueObject\BusinessTripId;
use App\Employee\Domain\ValueObject\EmployeeId;

interface BusinessTripRepository
{
    public function save(BusinessTrip $businessTrip): void;

    public function findOne(BusinessTripId $id): ?BusinessTrip;

    public function findByEmployeeId(EmployeeId $employeeId): array;
}
