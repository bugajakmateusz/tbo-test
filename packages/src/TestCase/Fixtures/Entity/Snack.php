<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Fixtures\Entity;

final readonly class Snack
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
