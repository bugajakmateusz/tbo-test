<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Mother;

use Tab\Domain\Email;
use Tab\Domain\Model\User\Name;
use Tab\Domain\Model\User\Password;
use Tab\Domain\Model\User\Role;
use Tab\Domain\Model\User\User;
use Tab\Packages\Faker\Faker;
use Tab\Tests\TestCase\Application\Mock\FakePasswordHasher;
use Tab\Tests\TestCase\Application\Mock\FakeUserRepository;

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
