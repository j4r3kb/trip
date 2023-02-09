<?php

declare(strict_types=1);

namespace App\BusinessTrip\Application\Query;

use JsonSerializable;

class BusinessTripView implements JsonSerializable
{
    public function __construct(
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly string $countryCode,
        public readonly int $amountDue,
        public readonly string $currency
    )
    {
    }

    public static function fromArray(array $data): static
    {
        return new static(...array_values($data));
    }

    public function jsonSerialize(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'countryCode' => mb_strtoupper($this->countryCode),
            'amountDue' => $this->amountDue,
            'currency' => mb_strtoupper($this->currency),
        ];
    }
}
