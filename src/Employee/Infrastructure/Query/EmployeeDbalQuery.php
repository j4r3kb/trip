<?php

declare(strict_types=1);

namespace App\Employee\Infrastructure\Query;

use App\Common\Infrastructure\Query\AbstractDbalQuery;
use App\Employee\Application\Query\EmployeeQuery;

class EmployeeDbalQuery extends AbstractDbalQuery implements EmployeeQuery
{
    public function employeeExists(string $employeeId): bool
    {
        return (bool) $this->connection->executeQuery(
            "SELECT EXISTS(SELECT 1 FROM employee WHERE id=:id)",
            [
                'id' => $employeeId
            ]
        )->fetchOne();
    }
}
