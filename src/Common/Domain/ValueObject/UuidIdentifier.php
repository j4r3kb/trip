<?php

declare(strict_types=1);

namespace App\Common\Domain\ValueObject;

use App\Common\Domain\Exception\InvalidIdentifierFormatException;
use Stringable;
use Symfony\Component\Uid\Uuid;

abstract class UuidIdentifier implements Stringable
{
    protected string $value;

    protected function __construct(?string $value = null)
    {
        if ($value === null) {
            $this->value = Uuid::v4()->toRfc4122();

            return;
        }

        if (Uuid::isValid($value) === false) {
            throw InvalidIdentifierFormatException::create($value);
        }

        $this->value = $value;
    }

    public static function create(): static
    {
        return new static(Uuid::v4()->toRfc4122());
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(UuidIdentifier $other): bool
    {
        return $this->value === $other->value;
    }
}
