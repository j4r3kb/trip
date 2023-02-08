<?php

declare(strict_types=1);

namespace App\Employee\Domain\Entity;

use App\Employee\Domain\ValueObject\EmployeeId;

class Employee
{
    private readonly string $id;

    private function __construct(EmployeeId $id)
    {
        $this->id = $id->__toString();
    }

    public static function create(): static
    {
        return new static(EmployeeId::create());
    }

    public function id(): EmployeeId
    {
        return EmployeeId::fromString($this->id);
    }
}
