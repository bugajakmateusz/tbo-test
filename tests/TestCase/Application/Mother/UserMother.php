<?php

declare(strict_types=1);

namespace Polsl\Tests\TestCase\Application\Mother;

use Polsl\Domain\Email;
use Polsl\Domain\Model\User\Name;
use Polsl\Domain\Model\User\Password;
use Polsl\Domain\Model\User\Role;
use Polsl\Domain\Model\User\User;
use Polsl\Packages\Faker\Faker;
use Polsl\Tests\TestCase\Application\Mock\FakePasswordHasher;
use Polsl\Tests\TestCase\Application\Mock\FakeUserRepository;

final class UserMother
{
    public static function random(): User
    {
        return self::create();
    }

    private static function create(
        ?string $email = null,
    ): User {
        $userRepository = new FakeUserRepository();
        $passwordHasher = FakePasswordHasher::getInstance();

        $email = Email::fromString(
            $email ?? Faker::email(),
        );
        $password = Password::hash(
            Faker::password(),
            $passwordHasher,
        );
        $name = Name::fromString(
            Faker::firstName(),
        );
        $surname = Name::fromString(
            Faker::lastName(),
        );
        $roles = [Role::USER];

        return User::register(
            $email,
            $password,
            $name,
            $surname,
            $userRepository,
            ...$roles,
        );
    }
}
