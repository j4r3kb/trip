<?php

declare(strict_types=1);

namespace App\Tests\Unit\BusinessTrip\Domain\ValueObject;

use App\BusinessTrip\Domain\Exception\AllowanceAmountLowerThanMinimumException;
use App\BusinessTrip\Domain\ValueObject\AllowancePerDay;
use PHPUnit\Framework\TestCase;

class AllowancePerDayTest extends TestCase
{
    public function testThrowsExceptionWhenAmountLowerThanMinimum(): void
    {
        $this->expectException(AllowanceAmountLowerThanMinimumException::class);
        AllowancePerDay::create(-1, 'PLN');
    }
}
