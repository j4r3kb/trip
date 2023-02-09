<?php

declare(strict_types=1);

namespace App\BusinessTrip\Application\Query;

use JsonSerializable;

class BusinessTripListView implements JsonSerializable
{
    /**
     * @var BusinessTripView[] $items
     */
    public array $items;

    public static function create(): static
    {
        return new static();
    }

    public function add(BusinessTripView $item): void
    {
        $this->items[] = $item;
    }

    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = $item->jsonSerialize();
        }

        return $result;
    }
}
