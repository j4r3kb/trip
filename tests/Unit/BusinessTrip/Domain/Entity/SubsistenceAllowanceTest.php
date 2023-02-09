<?php

declare(strict_types=1);

namespace App\Tests\Unit\BusinessTrip\Domain\Entity;

use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use App\BusinessTrip\Domain\Exception\AllowanceAmountLowerThanMinimumException;
use App\BusinessTrip\Domain\Exception\InvalidAlpha2CountryCodeException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use PHPUnit\Framework\TestCase;

class SubsistenceAllowanceTest extends TestCase
{
    public function testThrowsExceptionWhenInvalidCountryCodeProvided(): void
    {
        $this->expectException(InvalidAlpha2CountryCodeException::class);
        SubsistenceAllowance::create('XYZ', 100);
    }

    public function testCanNotUseAmountLowerThanMinimum(): void
    {
        $this->expectException(AllowanceAmountLowerThanMinimumException::class);
        SubsistenceAllowance::create('pl', -1);
    }

    public function testCountryDefaultCurrencyIsUsedWhenNoneProvided(): void
    {
        $subsistenceAllowance = SubsistenceAllowance::create('pl', 100);
        $this->assertTrue(
            Money::of(100, 'PLN')->isEqualTo($subsistenceAllowance->allowancePerDay->money())
        );
    }

    public function testProvidedCurrencyIsUsedWhenValid(): void
    {
        $subsistenceAllowance = SubsistenceAllowance::create('pl', 100, 'EUR');
        $this->assertTrue(
            Money::of(100, 'EUR')->isEqualTo($subsistenceAllowance->allowancePerDay->money())
        );
    }

    public function testThrowsExceptionWhenInvalidCurrencyCodeProvided(): void
    {
        $this->expectException(UnknownCurrencyException::class);
        SubsistenceAllowance::create('pl', 100, 'XYZ');
    }
}
