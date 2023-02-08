<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Exception;

use DomainException;

class AllowanceAmountLowerThanMinimumException extends DomainException
{
    public static function create(int $amount): static
    {
        return new static('Amount of %d is lower than minimum allowance');
    }
}
