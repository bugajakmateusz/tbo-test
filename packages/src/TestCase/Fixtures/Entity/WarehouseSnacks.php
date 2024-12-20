<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Fixtures\Entity;

final class WarehouseSnacks
{
    public function __construct(
        public int $id,
        public int $quantity,
    ) {}
}
