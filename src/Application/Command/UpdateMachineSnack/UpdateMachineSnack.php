<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateMachineSnack;

use Polsl\Infrastructure\Symfony\Validator\Constraints\NotEnoughQuantity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

#[NotEnoughQuantity]
final readonly class UpdateMachineSnack
{
    public function __construct(
        public int $id,
        #[NotBlank]
        #[Positive]
        public int $quantity,
    ) {}
}
