<?php

declare(strict_types=1);

namespace App\Common\Application\Command;

abstract class AbstractCreationCommand implements CreationCommand
{
    protected ?string $createdId = null;

    public function setCreatedId(string $id): void
    {
        $this->createdId = $id;
    }

    public function createdId(): ?string
    {
        return $this->createdId;
    }
}
