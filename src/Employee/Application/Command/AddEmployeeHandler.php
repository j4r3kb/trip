<?php

declare(strict_types=1);

namespace App\Employee\Application\Command;

use App\Common\Application\Command\CommandHandlerInterface;
use App\Employee\Domain\Entity\Employee;
use App\Employee\Domain\Repository\EmployeeRepository;

class AddEmployeeHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository
    )
    {
    }

    public function __invoke(AddEmployeeCommand $command): void
    {
        $employee = Employee::create();
        $this->employeeRepository->save($employee);
        $command->setCreatedId($employee->id()->__toString());
    }
}
