<?php

declare(strict_types=1);

namespace App\UserInterface\Api\Controller\ValueResolver;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

class BodyValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer
    )
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if (!$argumentType || !class_exists($argumentType)) {
            return [];
        }

        $format = $request->getContentTypeFormat();
        if (!in_array($format, ['json', 'xml'])) {
            return [];
        }

        try {
            yield $this->serializer->deserialize($request->getContent(), $argumentType, $format);
        } catch (Exception $exception) {
            throw new BadRequestException('Invalid data provided', 400);
        }
    }
}
