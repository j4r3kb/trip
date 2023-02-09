<?php

declare(strict_types=1);

namespace App\Tests\Integration\BusinessTrip\Application\Command;

use App\BusinessTrip\Application\Command\AddSubsistenceAllowanceCommand;
use App\BusinessTrip\Application\Command\AddSubsistenceAllowanceHandler;
use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use App\BusinessTrip\Domain\Repository\SubsistenceAllowanceRepository;
use App\BusinessTrip\Domain\ValueObject\SubsistenceAllowanceId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddSubsistenceAllowanceHandlerTest extends KernelTestCase
{
    public function testSubsistenceAllowanceIsAddedToRepository(): void
    {
        $container = $this->getContainer();
        $subsistenceAllowanceRepository = $container->get(SubsistenceAllowanceRepository::class);
        $handler = $container->get(AddSubsistenceAllowanceHandler::class);
        $command = new AddSubsistenceAllowanceCommand('pl', 100, 'PLN');

        $handler->__invoke($command);

        $this->assertIsString($command->createdId());
        $this->assertInstanceOf(
            SubsistenceAllowance::class,
            $subsistenceAllowanceRepository->findOne(SubsistenceAllowanceId::fromString('pl'))
        );
    }
}
