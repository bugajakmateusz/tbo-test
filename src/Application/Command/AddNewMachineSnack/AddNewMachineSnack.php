<?php

declare(strict_types=1);

namespace Tab\Application\Command\AddNewMachineSnack;

final readonly class AddNewMachineSnack
{
    public function __construct(
        public int $machineId,
        public int $snackId,
        public int $quantity,
        public string $position,
        public float $price,
    ) {}
}
