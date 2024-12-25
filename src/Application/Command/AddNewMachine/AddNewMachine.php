<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewMachine;

final readonly class AddNewMachine
{
    public function __construct(
        public string $location,
        public int $positionsNumber,
        public int $positionsCapacity,
    ) {}
}
