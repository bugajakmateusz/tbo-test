<?php

declare(strict_types=1);

namespace Tab\Domain\Model\User;

use Tab\Domain\DomainException;
use Tab\Domain\Email;

class User
{
    public int $id;

    /** @param Role[] $roles */
    private function __construct(
        public string $email,
        public string $pass_hash,
        public string $name,
        public string $surname,
        public array $roles,
    ) {
    }

    public static function register(
        Email $email,
        Password $password,
        Name $name,
        Name $surname,
        UserRepositoryInterface $userRepository,
        Role ...$roles,
    ): self {
        $stringEmail = $email->toString();
        $existingUser = $userRepository->findByEmail($stringEmail);
        if (null !== $existingUser) {
            throw new DomainException("User with email {$stringEmail} already exists");
        }

        return new self(
            $email->toString(),
            $password->toString(),
            $name->toString(),
            $surname->toString(),
            $roles,
        );
    }
}
