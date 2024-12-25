<?php

declare(strict_types=1);

namespace Polsl\Application\Command\AddNewSnack;

final readonly class AddNewSnack
{
    public function __construct(
        public string $name,
    ) {}
}
