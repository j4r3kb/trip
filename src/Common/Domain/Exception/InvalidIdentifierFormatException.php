<?php

declare(strict_types=1);

namespace App\Common\Domain\Exception;

use RuntimeException;

class InvalidIdentifierFormatException extends RuntimeException
{
    public static function create(string $value): static
    {
        return new static(sprintf('Given identifier %s is not UUID', $value));
    }
}
