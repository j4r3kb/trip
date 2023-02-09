<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\ValueObject;

use Stringable;

final class SubsistenceAllowanceId implements Stringable
{
    private function __construct(
        private readonly string $value
    )
    {
    }

    public static function fromString(string $countryAlpha2): self
    {
        return new self(mb_strtolower($countryAlpha2));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(SubsistenceAllowanceId $other): bool
    {
        return $this->value === $other->value;
    }
}
