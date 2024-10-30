<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Mother\Entity;

use Symfony\Component\PasswordHasher\Hasher\SodiumPasswordHasher;
use Tab\Packages\Constants\Hasher;
use Tab\Packages\Faker\Faker;
use Tab\Packages\PasswordHasher\PasswordHasherInterface;
use Tab\Packages\PasswordHasher\SymfonySodiumPasswordHasher;
use Tab\Packages\TestCase\Fixtures\Entity\User;

final class UserMother
{
    private static ?PasswordHasherInterface $passwordHasher = null;

    public static function random(): User
    {
        $password = Faker::password();

        return new User(
            Faker::intId(),
            Faker::name(),
            Faker::lastName(),
            Faker::email(),
            $password,
            self::hashUserPassword($password),
            ['ROLE_USER'],
        );
    }

    public static function hashUserPassword(string $plainPassword): string
    {
        $passwordHasher = self::$passwordHasher ??= new SymfonySodiumPasswordHasher(
            new SodiumPasswordHasher(
                Hasher::TEST_SODIUM_TIME_COST,
                Hasher::TEST_SODIUM_MEMORY_LIMIT_IN_BYTES,
            ),
        );

        return $passwordHasher->hash($plainPassword);
    }
}
