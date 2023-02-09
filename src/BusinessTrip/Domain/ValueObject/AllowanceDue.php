<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\ValueObject;

use App\BusinessTrip\Domain\Exception\AllowanceAmountLowerThanMinimumException;
use Brick\Money\Money;

final class AllowanceDue
{
    private readonly int $amount;

    private readonly string $currency;

    private function __construct(Money $allowance)
    {
        $this->amount = $allowance->getAmount()->toInt();
        $this->currency = $allowance->getCurrency()->getCurrencyCode();
    }

    public static function create(int $amount, string $currency): self
    {
        if ($amount < 0) {
            throw AllowanceAmountLowerThanMinimumException::create($amount);
        }

        return new self(Money::of($amount, $currency));
    }

    public static function fromMoney(Money $money): self
    {
        if ($money->getAmount()->toInt() < 0) {
            throw AllowanceAmountLowerThanMinimumException::create($money->getAmount()->toInt());
        }

        return new self($money);
    }

    public function money(): Money
    {
        return Money::of($this->amount, $this->currency);
    }
}
