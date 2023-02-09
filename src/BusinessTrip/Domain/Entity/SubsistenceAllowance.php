<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Entity;

use App\BusinessTrip\Domain\Exception\AllowanceAmountLowerThanMinimumException;
use App\BusinessTrip\Domain\Exception\InvalidAlpha2CountryCodeException;
use App\BusinessTrip\Domain\ValueObject\AllowancePerDay;
use App\BusinessTrip\Domain\ValueObject\SubsistenceAllowanceId;
use Brick\Money\Currency;
use League\ISO3166\Exception\DomainException;
use League\ISO3166\Exception\OutOfBoundsException;
use League\ISO3166\ISO3166;

class SubsistenceAllowance
{
    private readonly string $countryAlpha2;

    private readonly int $allowanceAmount;

    private readonly string $allowanceCurrency;

    private function __construct(
        SubsistenceAllowanceId $countryAlpha2,
        public readonly AllowancePerDay $allowancePerDay
    )
    {
        $this->countryAlpha2 = $countryAlpha2->__toString();
    }

    public static function create(
        string $countryAlpha2,
        int $amountPerDay,
        ?string $currency = null
    ): static
    {
        $countryAlpha2 = mb_strtolower($countryAlpha2);
        try {
            $country = (new ISO3166())->alpha2($countryAlpha2);
        } catch (OutOfBoundsException | DomainException) {
            throw InvalidAlpha2CountryCodeException::create($countryAlpha2);
        }

        if ($amountPerDay <= 0) { // this limit could be set f.e. by policy
            throw AllowanceAmountLowerThanMinimumException::create($amountPerDay);
        }

        if ($currency) {
            $currency = Currency::of($currency)->getCurrencyCode();
        } else {
            $currency = current($country['currency']);
        }

        return new static(
            SubsistenceAllowanceId::fromString($countryAlpha2),
            AllowancePerDay::create($amountPerDay, $currency)
        );
    }

    public function id(): SubsistenceAllowanceId
    {
        return SubsistenceAllowanceId::fromString($this->countryAlpha2);
    }
}
