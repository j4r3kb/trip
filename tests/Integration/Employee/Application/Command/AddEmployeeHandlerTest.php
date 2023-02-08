<?php

declare(strict_types=1);

namespace App\Tests\Integration\Employee\Application\Command;

use App\Employee\Application\Command\AddEmployeeCommand;
use App\Employee\Application\Command\AddEmployeeHandler;
use App\Employee\Domain\Entity\Employee;
use App\Employee\Domain\Repository\EmployeeRepository;
use App\Employee\Domain\ValueObject\EmployeeId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddEmployeeHandlerTest extends KernelTestCase
{
    public function testEmployeeIsAddedToRepository(): void
    {
        $container = $this->getContainer();
        $employeeRepository = $container->get(EmployeeRepository::class);
        $handler = $container->get(AddEmployeeHandler::class);
        $command = new AddEmployeeCommand();

        $handler->__invoke($command);

        $this->assertIsString($command->createdId());
        $this->assertInstanceOf(
            Employee::class,
            $employeeRepository->findOne(EmployeeId::fromString($command->createdId()))
        );
    }
}
