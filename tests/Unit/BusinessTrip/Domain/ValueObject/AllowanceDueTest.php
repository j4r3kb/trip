<?php

declare(strict_types=1);

namespace App\Tests\Unit\BusinessTrip\Domain\ValueObject;

use App\BusinessTrip\Domain\Exception\AllowanceAmountLowerThanMinimumException;
use App\BusinessTrip\Domain\ValueObject\AllowanceDue;
use PHPUnit\Framework\TestCase;

class AllowanceDueTest extends TestCase
{
    public function testThrowsExceptionWhenAmountLowerThanMinimum(): void
    {
        $this->expectException(AllowanceAmountLowerThanMinimumException::class);
        AllowanceDue::create(-1, 'PLN');
    }
}
