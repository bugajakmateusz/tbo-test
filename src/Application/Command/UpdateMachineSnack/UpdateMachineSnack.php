<?php

declare(strict_types=1);

namespace Tab\Application\Command\UpdateMachineSnack;

final readonly class UpdateMachineSnack
{
    public function __construct(
        public int $id,
        public int $quantity,
    ) {}
}
