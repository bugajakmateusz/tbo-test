<?php

declare(strict_types=1);

namespace Tab\Application\Command\RemoveMachine;

final readonly class RemoveMachine
{
    public function __construct(
        public int $id,
    ) {}
}
