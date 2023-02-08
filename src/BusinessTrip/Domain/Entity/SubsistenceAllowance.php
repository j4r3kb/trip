<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Entity;

use App\BusinessTrip\Domain\Exception\AllowanceAmountLowerThanMinimumException;
use App\BusinessTrip\Domain\Exception\InvalidAlpha2CountryCodeException;
use App\BusinessTrip\Domain\ValueObject\SubsistenceAllowanceId;
use Brick\Money\Currency;
use Brick\Money\Money;
use League\ISO3166\Exception\DomainException;
use League\ISO3166\Exception\OutOfBoundsException;
use League\ISO3166\ISO3166;

class SubsistenceAllowance
{
    private readonly string $countryAlpha2;

    private function __construct(
        SubsistenceAllowanceId $countryAlpha2,
        private readonly int $allowanceAmount,
        private readonly string $allowanceCurrency
    )
    {
        $this->countryAlpha2 = $countryAlpha2->__toString();
    }

    public static function create(
        string $countryAlpha2,
        int $allowanceAmount,
        ?string $allowanceCurrency = null
    ): static
    {
        try {
            $country = (new ISO3166())->alpha2($countryAlpha2);
        } catch (OutOfBoundsException | DomainException) {
            throw InvalidAlpha2CountryCodeException::create($countryAlpha2);
        }

        if ($allowanceAmount <= 0) { // this limit could be set f.e. by policy
            throw AllowanceAmountLowerThanMinimumException::create($allowanceAmount);
        }

        if ($allowanceCurrency) {
            $currency = Currency::of($allowanceCurrency)->getCurrencyCode();
        } else {
            $currency = current($country['currency']);
        }

        return new static(SubsistenceAllowanceId::create($countryAlpha2), $allowanceAmount, $currency);
    }

    public function id(): SubsistenceAllowanceId
    {
        return SubsistenceAllowanceId::create($this->countryAlpha2);
    }

    public function allowance(): Money
    {
        return Money::of($this->allowanceAmount, $this->allowanceCurrency);
    }
}
