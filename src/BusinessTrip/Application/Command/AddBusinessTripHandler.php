<?php

declare(strict_types=1);

namespace App\BusinessTrip\Application\Command;

use App\BusinessTrip\Domain\Entity\BusinessTrip;
use App\BusinessTrip\Domain\Exception\BusinessTripDurationOverlapException;
use App\BusinessTrip\Domain\Exception\CountryCodeNotSupportedException;
use App\BusinessTrip\Domain\Repository\BusinessTripRepository;
use App\BusinessTrip\Domain\Repository\SubsistenceAllowanceRepository;
use App\BusinessTrip\Domain\ValueObject\BusinessTripDuration;
use App\BusinessTrip\Domain\ValueObject\SubsistenceAllowanceId;
use App\Common\Application\Command\CommandHandlerInterface;
use App\Employee\Domain\Exception\EmployeeNotFoundException;
use App\Employee\Domain\Repository\EmployeeRepository;
use App\Employee\Domain\ValueObject\EmployeeId;

class AddBusinessTripHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly BusinessTripRepository $businessTripRepository,
        private readonly EmployeeRepository $employeeRepository,
        private readonly SubsistenceAllowanceRepository $subsistenceAllowanceRepository
    )
    {
    }

    public function __invoke(AddBusinessTripCommand $command): void
    {
        $employeeId = EmployeeId::fromString($command->employeeId);
        $employee = $this->employeeRepository->findOne($employeeId);
        if ($employee === null) {
            throw EmployeeNotFoundException::create($command->employeeId);
        }

        $subsistenceAllowanceId = SubsistenceAllowanceId::fromString($command->countryAlpha2);
        $subsistenceAllowance = $this->subsistenceAllowanceRepository->findOne($subsistenceAllowanceId);
        if ($subsistenceAllowance === null) {
            throw CountryCodeNotSupportedException::create($command->countryAlpha2);
        }

        $duration = BusinessTripDuration::create($command->startDate, $command->endDate);

        $employeeBusinessTrips = $this->businessTripRepository->findByEmployeeId($employeeId);
        foreach ($employeeBusinessTrips as $other) {
            if ($other->overlapsWith($duration)) {
                throw BusinessTripDurationOverlapException::create(
                    $duration->startDate(),
                    $duration->endDate(),
                    $other->duration->startDate(),
                    $other->duration->endDate()
                );
            }
        }

        $businessTrip = BusinessTrip::create(
            $employeeId,
            $subsistenceAllowance->id()->__toString(),
            $duration,
            $subsistenceAllowance->allowancePerDay
        );
        $this->businessTripRepository->save($businessTrip);
    }
}
