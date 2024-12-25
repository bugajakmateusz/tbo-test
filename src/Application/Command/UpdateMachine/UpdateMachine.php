<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateMachine;

final readonly class UpdateMachine
{
    public function __construct(
        public int $id,
        public ?string $location,
        public ?int $positionsNumber,
        public ?int $positionsCapacity,
    ) {}
}
