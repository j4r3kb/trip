<?php

declare(strict_types=1);

namespace App\BusinessTrip\Application\Command;

use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use App\BusinessTrip\Domain\Repository\SubsistenceAllowanceRepository;
use App\Common\Application\Command\CommandHandlerInterface;

class AddSubsistenceAllowanceHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly SubsistenceAllowanceRepository $repository
    )
    {
    }

    public function __invoke(AddSubsistenceAllowanceCommand $command): void
    {
        $subsistenceAllowance = SubsistenceAllowance::create(
            $command->countryAlpha2,
            $command->allowanceAmount,
            $command->allowanceCurrency
        );

        $this->repository->save($subsistenceAllowance);
        $command->setCreatedId($subsistenceAllowance->id()->__toString());
    }
}
