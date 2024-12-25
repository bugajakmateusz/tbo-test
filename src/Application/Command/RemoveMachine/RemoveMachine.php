<?php

declare(strict_types=1);

namespace Polsl\Application\Command\RemoveMachine;

final readonly class RemoveMachine
{
    public function __construct(
        public int $id,
    ) {}
}
