<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateSnackPrice;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

final readonly class UpdateSnackPrice
{
    public function __construct(
        #[NotBlank]
        public int $machineId,
        #[NotBlank]
        public int $snackId,
        #[NotBlank]
        #[Positive]
        public float $price,
    ) {}
}
