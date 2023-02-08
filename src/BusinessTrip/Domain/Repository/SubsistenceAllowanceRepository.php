<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Repository;

use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;

interface SubsistenceAllowanceRepository
{
    public function save(SubsistenceAllowance $subsistenceAllowance): void;

    public function findOne(string $countryCode): ?SubsistenceAllowance;
}
