<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Exception;

use DomainException;

class CountryCodeNotSupportedException extends DomainException
{
    public static function create(string $countryAlpha2): static
    {
        return new static(sprintf('Country %s is not supported', $countryAlpha2), 400);
    }
}
