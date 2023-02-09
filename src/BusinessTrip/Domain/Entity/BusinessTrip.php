<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Entity;

use App\BusinessTrip\Domain\ValueObject\AllowanceDue;
use App\BusinessTrip\Domain\ValueObject\AllowancePerDay;
use App\BusinessTrip\Domain\ValueObject\BusinessTripDuration;
use App\BusinessTrip\Domain\ValueObject\BusinessTripId;
use App\Employee\Domain\ValueObject\EmployeeId;
use Carbon\CarbonInterface;

class BusinessTrip
{
    private const MIN_DAY_HOURS = 8;

    private const EXTRA_RATE_MULTIPLIER = 2;

    private readonly string $id;

    private readonly string $employeeId;

    private function __construct(
        BusinessTripId $id,
        EmployeeId $employeeId,
        public readonly string $countryCode,
        public readonly BusinessTripDuration $duration,
        public readonly AllowanceDue $allowanceDue
    )
    {
        $this->id = $id->__toString();
        $this->employeeId = $employeeId->__toString();
    }

    public static function create(
        EmployeeId $employeeId,
        string $countryCode,
        BusinessTripDuration $duration,
        AllowancePerDay $allowancePerDay
    ): static
    {
        return new static(
            BusinessTripId::create(),
            $employeeId,
            $countryCode,
            $duration,
            static::allowanceDue($allowancePerDay, $duration)
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

    public function overlapsWith(BusinessTripDuration $otherDuration): bool
    {
        return $this->duration->overlapsWith($otherDuration);
    }

    public static function allowanceDue(AllowancePerDay $allowancePerDay, BusinessTripDuration $duration): AllowanceDue
    {
        $startDate = $duration->startDate();
        $endDate = $duration->endDate();

        $subsistenceDays = 0;
        if ($startDate->startOfDay()->diffInWeeks($endDate->startOfDay()) > 0) {
            $startPlusSevenDays = $startDate->addDays(7)->endOfDay();
            $startPlusEightDays = $startDate->addDays(8)->startOfDay();
            $subsistenceDays += static::calculateSubsistenceDays($startDate, $startPlusSevenDays);
            $subsistenceDays += static::calculateSubsistenceDays($startPlusEightDays, $endDate)
                * static::EXTRA_RATE_MULTIPLIER;
        } else {
            $subsistenceDays += static::calculateSubsistenceDays($startDate, $endDate);
        }

        return AllowanceDue::fromMoney($allowancePerDay->money()->multipliedBy($subsistenceDays));
    }

    private static function calculateSubsistenceDays(CarbonInterface $startDate, CarbonInterface $endDate): int
    {
        $nextDayOrEndDate = min($startDate->addDay()->startOfDay(), $endDate);

        $subsistenceDays = (int) ($startDate->isWeekday()
            && ($startDate->diffInHours($nextDayOrEndDate) >= static::MIN_DAY_HOURS));
        if ($startDate->isSameDay($endDate) === false) {
            $endDayOrStartDate = max($endDate->startOfDay(), $startDate);
            $subsistenceDays += $nextDayOrEndDate->diffInWeekdays($endDayOrStartDate);
            $subsistenceDays += (int) ($endDate->isWeekday() &&
                $endDate->diffInHours($endDayOrStartDate) >= static::MIN_DAY_HOURS);
        }

        return $subsistenceDays;
    }
}
