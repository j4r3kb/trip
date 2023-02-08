<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Exception;

use OutOfBoundsException;

class InvalidAlpha2CountryCodeException extends OutOfBoundsException
{
    public static function create(string $countryCode): static
    {
        return new static(sprintf('Country code %s is not a valid Alpha2 code', $countryCode));
    }
}
