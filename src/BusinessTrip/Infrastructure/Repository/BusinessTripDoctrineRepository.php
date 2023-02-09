<?php

declare(strict_types=1);

namespace App\BusinessTrip\Infrastructure\Repository;

use App\BusinessTrip\Domain\Entity\BusinessTrip;
use App\BusinessTrip\Domain\Repository\BusinessTripRepository;
use App\BusinessTrip\Domain\ValueObject\BusinessTripId;
use App\Employee\Domain\ValueObject\EmployeeId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BusinessTripDoctrineRepository extends ServiceEntityRepository implements BusinessTripRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BusinessTrip::class);
    }

    public function save(BusinessTrip $businessTrip): void
    {
        $em = $this->getEntityManager();
        $em->persist($businessTrip);
    }

    public function findOne(BusinessTripId $id): ?BusinessTrip
    {
        return $this->find($id);
    }

    public function findByEmployeeId(EmployeeId $employeeId): array
    {
        return $this->findBy(['employeeId' => $employeeId]);
    }
}
