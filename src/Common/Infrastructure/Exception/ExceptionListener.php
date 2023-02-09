<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Exception;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class ExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HandlerFailedException && null !== $exception->getPrevious()) {
            $exception = $exception->getPrevious();
        }

        $response = new JsonResponse(
            ['error' => $exception->getMessage()],
            $exception->getCode() === 0 ? 500 : $exception->getCode()
        );
        $event->setResponse($response);
    }
}