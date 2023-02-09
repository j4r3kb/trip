<?php

declare(strict_types=1);

namespace App\UserInterface\Api\Controller;

use App\BusinessTrip\Application\Command\AddBusinessTripCommand;
use App\BusinessTrip\Application\DTO\BusinessTripDTO;
use App\BusinessTrip\Application\Query\BusinessTripQuery;
use App\Common\Application\Response\Link;
use App\Employee\Application\Query\EmployeeQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

class BusinessTripController
{
    public function __construct(
        private readonly BusinessTripQuery $businessTripQuery,
        private readonly EmployeeQuery $employeeQuery,
        private readonly MessageBusInterface $commandBus,
        private readonly RouterInterface $router
    )
    {
    }

    public function add(string $employeeId, BusinessTripDTO $businessTripDTO): JsonResponse
    {
        $command = new AddBusinessTripCommand(
            $employeeId,
            $businessTripDTO->startDate,
            $businessTripDTO->endDate,
            $businessTripDTO->countryCode
        );
        $this->commandBus->dispatch($command);

        return new JsonResponse(
            [
                '_links' => [
                    new Link(
                        $this->router->generate('employee-business-trip-list', ['employeeId' => $employeeId]),
                        Request::METHOD_GET,
                        'list all business trips of employee'
                    ),
                ]
            ],
            Response::HTTP_CREATED
        );
    }

    public function list(string $employeeId): JsonResponse
    {
        if ($this->employeeQuery->employeeExists($employeeId) === false) {
            throw new NotFoundHttpException(
                sprintf('Employee %s was not found', $employeeId),
                null,
                Response::HTTP_NOT_FOUND
            );
        }

        $businessTripListView = $this->businessTripQuery->listForEmployee($employeeId);

        return new JsonResponse(
            [
                'businessTrips' => $businessTripListView,
                '_links' => [
                    new Link(
                        $this->router->generate('employee-business-trip-add', ['employeeId' => $employeeId]),
                        Request::METHOD_POST,
                        'add business trip for employee'
                    ),
                ]
            ],
            Response::HTTP_OK
        );
    }
}
