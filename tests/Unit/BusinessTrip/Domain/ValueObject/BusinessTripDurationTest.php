<?php

namespace App\Tests\Unit\BusinessTrip\Domain\ValueObject;

use App\BusinessTrip\Domain\Exception\BusinessTripStartDateGreaterOrEqualEndDateException;
use App\BusinessTrip\Domain\ValueObject\BusinessTripDuration;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use PHPUnit\Framework\TestCase;

class BusinessTripDurationTest extends TestCase
{
    public function testThrowsExceptionWhenStartDateGreaterOrEqualEndDate() {
        $this->expectException(BusinessTripStartDateGreaterOrEqualEndDateException::class);
        BusinessTripDuration::create(
            CarbonImmutable::parse('2020-01-01 12:00:00'),
            CarbonImmutable::parse('2020-01-01 12:00:00')
        );
    }

    /**
     * @dataProvider durationDataProvider
     */
    public function testOverlapsWithReturnsProperResult(
        CarbonInterface $startDate,
        CarbonInterface $endDate,
        bool $expectedResult
    ): void
    {
        $durationOne = BusinessTripDuration::create(
            CarbonImmutable::parse('2020-01-15 08:00:00'),
            CarbonImmutable::parse('2020-01-20 16:00:00')
        );
        $durationTwo = BusinessTripDuration::create($startDate, $endDate);

        $this->assertEquals($expectedResult, $durationOne->overlapsWith($durationTwo));
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
                false,
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
}
