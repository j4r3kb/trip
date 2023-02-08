<?php

declare(strict_types=1);

namespace App\BusinessTrip\Infrastructure\Repository;

use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use App\BusinessTrip\Domain\Repository\SubsistenceAllowanceRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubsistenceAllowanceDoctrineRepository extends ServiceEntityRepository implements SubsistenceAllowanceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubsistenceAllowance::class);
    }

    public function save(SubsistenceAllowance $subsistenceAllowance): void
    {
        $em = $this->getEntityManager();
        $em->persist($subsistenceAllowance);
    }

    public function findOne(string $countryAlpha2): ?SubsistenceAllowance
    {
        return $this->find($countryAlpha2);
    }
}
