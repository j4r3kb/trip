<?php

declare(strict_types=1);

namespace App\BusinessTrip\Domain\Repository;

use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use App\BusinessTrip\Domain\ValueObject\SubsistenceAllowanceId;

interface SubsistenceAllowanceRepository
{
    public function save(SubsistenceAllowance $subsistenceAllowance): void;

    public function findOne(SubsistenceAllowanceId $id): ?SubsistenceAllowance;
}
