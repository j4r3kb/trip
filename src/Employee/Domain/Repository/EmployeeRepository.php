<?php

declare(strict_types=1);

namespace App\Employee\Domain\Repository;

use App\Employee\Domain\Entity\Employee;
use App\Employee\Domain\ValueObject\EmployeeId;

interface EmployeeRepository
{
    public function save(Employee $employee): void;

    public function findOne(EmployeeId $employeeId): ?Employee;
}
