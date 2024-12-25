<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Fixtures\Entity;

final class SnackPrice
{
    public function __construct(
        public int $id,
        public Machine $machine,
        public Snack $snack,
        public float $price,
        public \DateTimeImmutable $priceCreatedAt,
    ) {}
}
