<?php

declare(strict_types=1);

namespace App\Employee\Infrastructure\Repository;

use App\Employee\Domain\Entity\Employee;
use App\Employee\Domain\Repository\EmployeeRepository;
use App\Employee\Domain\ValueObject\EmployeeId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmployeeDoctrineRepository extends ServiceEntityRepository implements EmployeeRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function save(Employee $employee): void
    {
        $em = $this->getEntityManager();
        $em->persist($employee);
    }

    public function findOne(EmployeeId $employeeId): ?Employee
    {
        return $this->find($employeeId);
    }
}
