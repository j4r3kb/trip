<?php

declare(strict_types=1);

namespace App\Employee\Domain\Exception;

use RuntimeException;

class EmployeeNotFoundException extends RuntimeException
{
    public static function create(string $employeeId): static
    {
        return new static(sprintf('Employee %s was not found', $employeeId), 404);
    }
}
