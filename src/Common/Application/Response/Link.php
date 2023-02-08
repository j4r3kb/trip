<?php

declare(strict_types=1);

namespace App\Common\Application\Response;

class Link
{
    public function __construct(
        public readonly string $href,
        public readonly string $method,
        public readonly string $rel
    ) {
    }
}
