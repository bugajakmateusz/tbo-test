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
        return self::create(['ROLE_USER']);
    }

    public static function withEmail(string $email): User
    {
        return self::create(
            ['ROLE_USER'],
            $email,
        );
    }

    public static function admin(): User
    {
        return self::create(
            [
                'ROLE_USER',
                'ROLE_ADMIN',
            ],
        );
    }

    public static function warehouseOperator(): User
    {
        return self::create(
            [
                'ROLE_USER',
                'ROLE_WAREHOUSE_OPERATOR',
            ],
        );
    }

    /** @param string[] $roles */
    public static function create(
        array $roles,
        ?string $email = null,
    ): User {
        $password = Faker::password();

        return new User(
            Faker::intId(),
            Faker::name(),
            Faker::lastName(),
            $email ?? Faker::email(),
            $password,
            self::hashUserPassword($password),
            $roles,
        );
    }

    private static function hashUserPassword(string $plainPassword): string
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
