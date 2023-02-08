<?php

namespace App\Tests\Unit\BusinessTrip\Domain\ValueObject;

use App\BusinessTrip\Domain\Exception\BusinessTripStartDateGreaterOrEqualEndDateException;
use App\BusinessTrip\Domain\ValueObject\BusinessTripDuration;
use Carbon\CarbonImmutable;
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
}
