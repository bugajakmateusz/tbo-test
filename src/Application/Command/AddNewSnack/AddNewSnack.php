<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewSnack;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class AddNewSnack
{
    public function __construct(
        #[NotBlank(normalizer: 'trim')]
        #[Length(max: 255, normalizer: 'trim')]
        public string $name,
    ) {}
}
