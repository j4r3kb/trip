<?php

declare(strict_types=1);

namespace App\BusinessTrip\Application\Command;

use App\Common\Application\Command\AbstractCreationCommand;

class AddSubsistenceAllowanceCommand extends AbstractCreationCommand
{
    public function __construct(
        public readonly string $countryAlpha2,
        public readonly int $allowanceAmount,
        public readonly ?string $allowanceCurrency = null
    )
    {
    }
}
