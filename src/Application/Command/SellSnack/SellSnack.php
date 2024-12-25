<?php

declare(strict_types=1);

namespace Polsl\Application\Command\SellSnack;

final readonly class SellSnack
{
    public function __construct(
        public int $machineId,
        public int $snackId,
        public string $position,
    ) {}
}
