<?php

declare(strict_types=1);

namespace Polsl\Application\Command\BuySnack;

final readonly class BuySnack
{
    public function __construct(
        public int $snackId,
        public float $price,
        public int $quantity,
    ) {}
}
