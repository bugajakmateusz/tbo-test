<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Fixtures\Entity;

final readonly class MachineSnack
{
    public function __construct(
        public int $id,
        public Machine $machine,
        public Snack $snack,
        public string $position,
        public int $quantity,
    ) {}
}
