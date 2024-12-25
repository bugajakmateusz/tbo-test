<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateMachine;

use Polsl\Domain\Model\Machine\Location;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

final readonly class UpdateMachine
{
    public function __construct(
        public int $id,
        #[NotBlank(normalizer: 'trim', groups: ['changedLocation'])]
        #[Length(max: Location::MAX_LENGTH, normalizer: 'trim')]
        public ?string $location,
        #[PositiveOrZero]
        public ?int $positionsNumber,
        #[PositiveOrZero]
        public ?int $positionsCapacity,
    ) {}
}
