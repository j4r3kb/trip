<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Entity;

use App\BusinessTrip\Domain\ValueObject\BusinessTripDuration;
use App\BusinessTrip\Domain\ValueObject\BusinessTripId;
use App\BusinessTrip\Domain\ValueObject\SubsistenceAllowanceId;
use App\Employee\Domain\ValueObject\EmployeeId;
use Brick\Money\Money;
use Carbon\CarbonInterface;

class BusinessTrip
{
    private const MIN_DAY_HOURS = 8;

    private const EXTRA_RATE_DAYS_THRESHOLD = 7;

    private const EXTRA_RATE_MULTIPLIER = 2;

    private readonly string $id;

    private readonly string $employeeId;

    private readonly string $subsistenceAllowanceId;

    public function __construct(
        BusinessTripId $id,
        EmployeeId $employeeId,
        SubsistenceAllowanceId $subsistenceAllowanceId,
        private readonly BusinessTripDuration $duration
    )
    {
        $this->id = $id->__toString();
        $this->employeeId = $employeeId->__toString();
        $this->subsistenceAllowanceId = $subsistenceAllowanceId->__toString();
    }

    public static function create(
        EmployeeId $employeeId,
        SubsistenceAllowanceId $subsistenceAllowanceId,
        BusinessTripDuration $duration
    ): static
    {
        return new static(
            BusinessTripId::create(),
            $employeeId,
            $subsistenceAllowanceId,
            $duration
        );
    }

    public function id(): BusinessTripId
    {
        return BusinessTripId::fromString($this->id);
    }

    public function employeeId(): EmployeeId
    {
        return EmployeeId::fromString($this->employeeId);
    }

    public function subsistenceAllowanceId(): SubsistenceAllowanceId
    {
        return SubsistenceAllowanceId::create($this->subsistenceAllowanceId);
    }

    public function overlapsWith(BusinessTrip $other): bool
    {
        return $this->duration->overlapsWith($other->duration);
    }

    public function allowanceDue(SubsistenceAllowance $subsistenceAllowance): Money
    {
        $startDate = $this->duration->startDate();
        $endDate = $this->duration->endDate();

        $subsistenceDays = 0;
        if ($startDate->startOfDay()->diffInWeeks($endDate->startOfDay()) > 0) {
            $startPlusSevenDays = $startDate->addDays(7)->endOfDay();
            $startPlusEightDays = $startDate->addDays(8)->startOfDay();
            $subsistenceDays += $this->calculateSubsistenceDays($startDate, $startPlusSevenDays);
            $subsistenceDays += $this->calculateSubsistenceDays($startPlusEightDays, $endDate)
                * static::EXTRA_RATE_MULTIPLIER;
        } else {
            $subsistenceDays += $this->calculateSubsistenceDays($startDate, $endDate);
        }

        return $subsistenceAllowance->allowance()->multipliedBy($subsistenceDays);
    }

    private function calculateSubsistenceDays(CarbonInterface $startDate, CarbonInterface $endDate): int
    {
        $nextDayOrEndDate = min($startDate->addDay()->startOfDay(), $endDate);

        $subsistenceDays = (int) ($startDate->isWeekday()
            && ($startDate->diffInHours($nextDayOrEndDate) >= static::MIN_DAY_HOURS));

        if ($startDate->isSameDay($endDate) === false) {
            $endDayOrStartDate = max($endDate->startOfDay(), $startDate);
            $subsistenceDays += $nextDayOrEndDate->diffInWeekdays($endDayOrStartDate);
            $subsistenceDays += (int) ($endDate->diffInHours($endDayOrStartDate) >= static::MIN_DAY_HOURS);
        }

        return $subsistenceDays;
    }
}
