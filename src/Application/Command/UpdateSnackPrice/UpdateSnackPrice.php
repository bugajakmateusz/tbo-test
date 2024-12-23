<?php

declare(strict_types=1);

namespace Tab\Application\Command\UpdateSnackPrice;

final readonly class UpdateSnackPrice
{
    public function __construct(
        public int $machineId,
        public int $snackId,
        public float $price,
    ) {}
}
