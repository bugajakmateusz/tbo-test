<?php

declare(strict_types=1);

namespace Tab\Application\Command\UpdateSnack;

final readonly class UpdateSnack
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
