<?php

declare(strict_types=1);

namespace Polsl\Domain\Model\User;

use Polsl\Domain\DomainException;
use Polsl\Packages\PasswordHasher\PasswordHasherInterface;

final class Password
{
    public const MIN_LENGTH = 8;
    public const MAX_LENGTH = 64;

    private function __construct(private readonly string $password) {}

    public static function hash(string $password, PasswordHasherInterface $passwordHasher): self
    {
        $length = \mb_strlen($password);
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            $minLength = self::MIN_LENGTH;
            $maxLength = self::MAX_LENGTH;

            throw new DomainException(
                "Password length must be between {$minLength} and {$maxLength} characters. Given password has {$length} characters.",
            );
        }

        return new self(
            $passwordHasher->hash($password),
        );
    }

    public function toString(): string
    {
        return $this->password;
    }
}
