<?php

declare(strict_types=1);

namespace App\Tests\Unit\BusinessTrip\Domain\Entity;

use App\BusinessTrip\Domain\Entity\BusinessTrip;
use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use App\BusinessTrip\Domain\ValueObject\BusinessTripDuration;
use App\BusinessTrip\Domain\ValueObject\SubsistenceAllowanceId;
use App\Employee\Domain\ValueObject\EmployeeId;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class BusinessTripTest extends TestCase
{
    public function testOverlapsWithReturnsTrueWhenTwoBusinessTripsOverlap(): void
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
            BusinessTripDuration::create(
                CarbonImmutable::parse('2020-01-10 08:00:00'),
                CarbonImmutable::parse('2020-01-15 08:00:01')
            )
        );
        $businessTripThree = BusinessTrip::create(
            EmployeeId::create(),
            SubsistenceAllowanceId::create('pl'),
            BusinessTripDuration::create(
                CarbonImmutable::parse('2020-01-20 15:59:59'),
                CarbonImmutable::parse('2020-01-25 12:00:00')
            )
        );

        $this->assertTrue(
            $businessTripOne->overlapsWith($businessTripTwo)
        );

        $this->assertTrue(
            $businessTripOne->overlapsWith($businessTripThree)
        );
    }

    public function testOverlapsWithReturnsFalseWhenTwoBusinessTripsDoNotOverlap(): void
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
            BusinessTripDuration::create(
                CarbonImmutable::parse('2020-01-10 08:00:00'),
                CarbonImmutable::parse('2020-01-15 08:00:00')
            )
        );
        $businessTripThree = BusinessTrip::create(
            EmployeeId::create(),
            SubsistenceAllowanceId::create('pl'),
            BusinessTripDuration::create(
                CarbonImmutable::parse('2020-01-20 16:00:00'),
                CarbonImmutable::parse('2020-01-25 12:00:00')
            )
        );

        $this->assertFalse(
            $businessTripOne->overlapsWith($businessTripTwo)
        );

        $this->assertFalse(
            $businessTripOne->overlapsWith($businessTripThree)
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAllowanceDueIsCalculatedProperly(
        CarbonImmutable $startDate,
        CarbonImmutable $endDate,
        int $expectedAmount
    ): void
    {
        $subsistenceAllowance = SubsistenceAllowance::create('pl', 100);
        $businessTrip = BusinessTrip::create(
            EmployeeId::create(),
            SubsistenceAllowanceId::create('pl'),
            BusinessTripDuration::create($startDate, $endDate)
        );

        $this->assertTrue(
            Money::of($expectedAmount, 'PLN')->isEqualTo($businessTrip->allowanceDue($subsistenceAllowance))
        );
    }

    private function dataProvider(): array
    {
        return [
//            '2023-01-06 16:00:01 - 2023-01-16 07:59:59' => [
//                CarbonImmutable::parse('2023-01-06 16:00:01'),
//                CarbonImmutable::parse('2023-01-16 07:59:59'),
//                700,
//            ],
            '2023-01-06 16:00:00 - 2023-01-16 08:00:00' => [
                CarbonImmutable::parse('2023-01-06 16:00:00'),
                CarbonImmutable::parse('2023-01-16 08:00:00'),
                800,
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
