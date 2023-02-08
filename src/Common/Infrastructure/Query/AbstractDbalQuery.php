<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Query;

use Doctrine\DBAL\Connection;

class AbstractDbalQuery
{
    public function __construct(
        protected readonly Connection $connection
    )
    {
    }
}
