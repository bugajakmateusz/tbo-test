<?php

declare(strict_types=1);

namespace Polsl\Application\Command\BuySnack;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

final readonly class BuySnack
{
    public function __construct(
        #[NotBlank]
        public int $snackId,
        #[NotBlank]
        #[Positive]
        public float $price,
        #[NotBlank]
        #[Positive]
        public int $quantity,
    ) {}
}
