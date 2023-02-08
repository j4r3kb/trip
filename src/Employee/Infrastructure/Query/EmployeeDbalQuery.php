<?php

declare(strict_types=1);

namespace App\Employee\Infrastructure\Query;

use App\Common\Infrastructure\Query\AbstractDbalQuery;
use App\Employee\Application\Query\EmployeeQuery;

class EmployeeDbalQuery extends AbstractDbalQuery implements EmployeeQuery
{
}
