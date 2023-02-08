<?php

declare(strict_types=1);

namespace App\Tests\Unit\BusinessTrip\Domain\Entity;

use App\BusinessTrip\Domain\Entity\BusinessTrip;
use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use App\BusinessTrip\Domain\ValueObject\BusinessTripDuration;
use App\BusinessTrip\Domain\ValueObject\SubsistenceAllowanceId;
use App\Employee\Domain\ValueObject\EmployeeId;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use PHPUnit\Framework\TestCase;

class BusinessTripTest extends TestCase
{
    /**
     * @dataProvider durationDataProvider
     */
    public function testOverlapsWithReturnsProperResult(
        CarbonInterface $startDate,
        CarbonInterface $endDate,
        bool $expectedResult
    ): void
    {
        $businessTripOne = BusinessTrip::create(
            EmployeeId::create(),
            SubsistenceAllowanceId::create('pl'),
            BusinessTripDuration::create(
                CarbonImmutable::parse('2020-01-15 08:00:00'),
                CarbonImmutable::parse('2020-01-20 16:00:00')
            )
        );
        $businessTripTwo = BusinessTrip::create(
            EmployeeId::create(),
            SubsistenceAllowanceId::create('pl'),
            BusinessTripDuration::create($startDate, $endDate)
        );

        $this->assertEquals($expectedResult, $businessTripOne->overlapsWith($businessTripTwo));
    }

    /**
     * @dataProvider allowanceDataProvider
     */
    public function testAllowanceDueIsCalculatedProperly(
        CarbonInterface $startDate,
        CarbonInterface $endDate,
        int $expectedAmount
    ): void
    {
        $subsistenceAllowance = SubsistenceAllowance::create('pl', 100);
        $businessTrip = BusinessTrip::create(
            EmployeeId::create(),
            SubsistenceAllowanceId::create('pl'),
            BusinessTripDuration::create($startDate, $endDate)
        );

        $this->assertEquals($expectedAmount, $businessTrip->allowanceDue($subsistenceAllowance)->getAmount()->toInt());
    }

    private function durationDataProvider(): array
    {
        return [
            '2020-01-10 08:00:00 - 2020-01-15 08:00:00' => [
                CarbonImmutable::parse('2020-01-10 08:00:00'),
                CarbonImmutable::parse('2020-01-15 08:00:00'),
                false,
            ],
            '2020-01-20 16:00:00 - 2020-01-25 12:00:00' => [
                CarbonImmutable::parse('2020-01-20 16:00:00'),
                CarbonImmutable::parse('2020-01-25 12:00:00'),
                false
            ],
            '2020-01-10 08:00:00 - 2020-01-15 08:00:01' => [
                CarbonImmutable::parse('2020-01-10 08:00:00'),
                CarbonImmutable::parse('2020-01-15 08:00:01'),
                true,
            ],
            '2020-01-20 15:59:59 - 2020-01-25 12:00:00' => [
                CarbonImmutable::parse('2020-01-20 15:59:59'),
                CarbonImmutable::parse('2020-01-25 12:00:00'),
                true,
            ],
        ];
    }

    private function allowanceDataProvider(): array
    {
        return [
            '2023-01-06 16:00:01 - 2023-01-16 07:59:59' => [
                CarbonImmutable::parse('2023-01-06 16:00:01'),
                CarbonImmutable::parse('2023-01-16 07:59:59'),
                500,
            ],
            '2023-01-06 16:00:00 - 2023-01-16 08:00:00' => [
                CarbonImmutable::parse('2023-01-06 16:00:00'),
                CarbonImmutable::parse('2023-01-23 08:00:00'),
                1800,
            ],
            '2023-01-24 16:00:01 - 2023-01-25 07:59:59' => [
                CarbonImmutable::parse('2023-01-24 16:00:01'),
                CarbonImmutable::parse('2023-01-25 07:59:59'),
                0,
            ],
            '2023-01-24 16:00:00 - 2023-01-25 08:00:00' => [
                CarbonImmutable::parse('2023-01-24 16:00:00'),
                CarbonImmutable::parse('2023-01-25 08:00:00'),
                200,
            ],
            '2023-01-24 08:00:00 - 2023-01-24 15:59:59' => [
                CarbonImmutable::parse('2023-01-24 08:00:00'),
                CarbonImmutable::parse('2023-01-24 15:59:59'),
                0,
            ],
            '2023-01-24 08:00:00 - 2023-01-24 16:00:00' => [
                CarbonImmutable::parse('2023-01-24 08:00:00'),
                CarbonImmutable::parse('2023-01-24 16:00:00'),
                100,
            ],
        ];
    }
}
