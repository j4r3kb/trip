<?php

declare(strict_types=1);

namespace App\Employee\Application\Query;

interface EmployeeQuery
{
    public function employeeExists(string $employeeId): bool;
}
