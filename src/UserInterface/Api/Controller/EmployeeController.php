<?php

declare(strict_types=1);

namespace App\UserInterface\Api\Controller;

use App\Common\Application\Response\Link;
use App\Employee\Application\Command\AddEmployeeCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

class EmployeeController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly RouterInterface $router
    )
    {
    }

    public function add(): JsonResponse
    {
        $command = new AddEmployeeCommand();
        $this->commandBus->dispatch($command);
        $employeeId = $command->createdId();

        return new JsonResponse(
            [
                'employeeId' => $employeeId,
                '_links' => [
                    new Link(
                        $this->router->generate('employee-business-trip-add', ['employeeId' => $employeeId]),
                        Request::METHOD_POST,
                        'add business trip for employee'
                    ),
                ]
            ],
            Response::HTTP_CREATED
        );
    }
}
