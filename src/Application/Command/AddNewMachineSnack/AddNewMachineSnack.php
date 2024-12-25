<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewMachineSnack;

use Polsl\Domain\Model\Machine\SnackPosition;
use Polsl\Infrastructure\Symfony\Validator\Constraints\MachinePositionTaken;
use Polsl\Infrastructure\Symfony\Validator\Constraints\NotEnoughQuantity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

#[MachinePositionTaken]
#[NotEnoughQuantity]
final readonly class AddNewMachineSnack
{
    public function __construct(
        #[NotBlank]
        #[Positive]
        public int $machineId,
        #[NotBlank]
        #[Positive]
        public int $snackId,
        #[NotBlank]
        #[Positive]
        public int $quantity,
        #[NotBlank(normalizer: 'trim')]
        #[Length(max: SnackPosition::MAX_LENGTH, normalizer: 'trim')]
        public string $position,
    ) {}
}
