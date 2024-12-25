<?php

declare(strict_types=1);

namespace Polsl\Packages\PasswordHasher;

interface PasswordHasherInterface
{
    public function hash(string $plainPassword): string;
}
