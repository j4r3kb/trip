<?php

declare(strict_types=1);

namespace App\BusinessTrip\Application\Query;

interface BusinessTripQuery
{
    public function listForEmployee(string $employeeId): BusinessTripListView;
}
