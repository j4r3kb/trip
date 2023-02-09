<?php

declare(strict_types=1);

namespace App\BusinessTrip\Infrastructure\Query;

use App\BusinessTrip\Application\Query\BusinessTripListView;
use App\BusinessTrip\Application\Query\BusinessTripQuery;
use App\BusinessTrip\Application\Query\BusinessTripView;
use App\Common\Infrastructure\Query\AbstractDbalQuery;

class BusinessTripDbalQuery extends AbstractDbalQuery implements BusinessTripQuery
{
    public function listForEmployee(string $employeeId): BusinessTripListView
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('b.start_date, b.end_date, b.country_code, b.allowance_due_amount, b.allowance_due_currency')
            ->from('business_trip', 'b')
            ->where('b.employee_id = :employeeId')
            ->orderBy('b.start_date', 'DESC')
            ->setParameter('employeeId', $employeeId)
        ;

        $result = $qb->executeQuery()->fetchAllAssociative();

        $list = BusinessTripListView::create();
        foreach ($result as $row) {
            $list->add(BusinessTripView::fromArray($row));
        }

        return $list;
    }
}
