<?php

declare(strict_types=1);

namespace Tab\Packages\PasswordHasher;

use Symfony\Component\PasswordHasher\Hasher\SodiumPasswordHasher;

final readonly class SymfonySodiumPasswordHasher implements PasswordHasherInterface
{
    public function __construct(private SodiumPasswordHasher $sodiumPasswordHasher) {}

    public function hash(string $plainPassword): string
    {
        return $this->sodiumPasswordHasher
            ->hash($plainPassword)
        ;
    }
}
