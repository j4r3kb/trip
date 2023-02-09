<?php

declare(strict_types=1);

namespace App\BusinessTrip\Infrastructure\Repository;

use App\BusinessTrip\Domain\Entity\SubsistenceAllowance;
use App\BusinessTrip\Domain\Repository\SubsistenceAllowanceRepository;
use App\BusinessTrip\Domain\ValueObject\SubsistenceAllowanceId;
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

    public function findOne(SubsistenceAllowanceId $id): ?SubsistenceAllowance
    {
        return $this->find($id);
    }
}
