<?php

declare(strict_types=1);

namespace Tab\Packages\PasswordHasher;

interface PasswordHasherInterface
{
    public function hash(string $plainPassword): string;
}
