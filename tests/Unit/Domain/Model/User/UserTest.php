<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\User;

use Tab\Domain\DomainException;
use Tab\Domain\Email;
use Tab\Domain\Model\User\Name;
use Tab\Domain\Model\User\Password;
use Tab\Domain\Model\User\Role;
use Tab\Domain\Model\User\User;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;
use Tab\Tests\TestCase\Application\Mock\FakePasswordHasher;
use Tab\Tests\TestCase\Application\Mock\FakeUserRepository;
use Tab\Tests\TestCase\Application\Mother\UserMother;

/**
 * @internal
 */
final class UserTest extends UnitTestCase
{
    public function test_user_can_be_created(): void
    {
        // Arrange
        $userRepository = new FakeUserRepository();
        $passwordHasher = FakePasswordHasher::getInstance();

        $email = Email::fromString(
            Faker::email(),
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

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        User::register(
            $email,
            $password,
            $name,
            $surname,
            $userRepository,
            ...$roles,
        );
    }

    public function test_user_without_roles_cannot_be_created(): void
    {
        // Arrange
        $userRepository = new FakeUserRepository();
        $passwordHasher = FakePasswordHasher::getInstance();

        $stringEmail = Faker::email();
        $email = Email::fromString($stringEmail);
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

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Roles list should not be empty.');

        // Act
        User::register(
            $email,
            $password,
            $name,
            $surname,
            $userRepository,
        );
    }

    public function test_user_with_existing_email_cannot_be_created(): void
    {
        // Arrange
        $userRepository = new FakeUserRepository(UserMother::random());
        $passwordHasher = FakePasswordHasher::getInstance();

        $stringEmail = Faker::email();
        $email = Email::fromString($stringEmail);
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

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage("User with email {$stringEmail} already exists");

        // Act
        User::register(
            $email,
            $password,
            $name,
            $surname,
            $userRepository,
            ...$roles,
        );
    }

    public function test_change_name(): void
    {
        // Arrange
        $user = UserMother::random();
        $name = Name::fromString(
            Faker::word(),
        );

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $user->changeName($name);
    }

    public function test_change_surname(): void
    {
        // Arrange
        $user = UserMother::random();
        $surname = Name::fromString(
            Faker::word(),
        );

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $user->changeSurname($surname);
    }

    public function test_change_email(): void
    {
        // Arrange
        $user = UserMother::random();
        $email = Email::fromString(
            Faker::email(),
        );

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $user->changeEmail(
            $email,
            new FakeUserRepository(),
        );
    }

    public function test_changing_email_is_not_possible_when_email_is_used(): void
    {
        // Arrange
        $user = UserMother::random();
        $stringEmail = Faker::email();
        $email = Email::fromString($stringEmail);

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage("User with email {$stringEmail} already exists");

        // Act
        $user->changeEmail(
            $email,
            new FakeUserRepository(
                UserMother::random(),
            ),
        );
    }

    public function test_change_password(): void
    {
        // Arrange
        $user = UserMother::random();
        $surname = Name::fromString(
            Faker::word(),
        );

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $user->changeSurname($surname);
    }

    public function test_change_roles(): void
    {
        // Arrange
        $user = UserMother::random();
        $roles = Faker::randomElements(Role::cases(), 2);

        // Expect
        self::expectNotToPerformAssertions();

        // Act
        $user->changeRoles(...$roles);
    }

    public function test_changing_roles_is_not_always_possible(): void
    {
        // Arrange
        $user = UserMother::random();

        // Expect
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Roles list should not be empty.');

        // Act
        $user->changeRoles();
    }
}
