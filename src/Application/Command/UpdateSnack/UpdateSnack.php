<?php

declare(strict_types=1);

namespace Polsl\Application\Command\UpdateSnack;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class UpdateSnack
{
    public function __construct(
        public int $id,
        #[NotBlank]
        #[Length(max: 255, normalizer: 'trim')]
        public string $name,
    ) {}
}
