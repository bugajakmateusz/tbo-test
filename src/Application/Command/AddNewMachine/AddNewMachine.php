<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewMachine;

use Polsl\Domain\Model\Machine\Location;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

final readonly class AddNewMachine
{
    public function __construct(
        #[NotBlank(normalizer: 'trim')]
        #[Length(max: Location::MAX_LENGTH, normalizer: 'trim')]
        public string $location,
        #[NotBlank]
        #[PositiveOrZero]
        public int $positionsNumber,
        #[NotBlank]
        #[PositiveOrZero]
        public int $positionsCapacity,
    ) {}
}
