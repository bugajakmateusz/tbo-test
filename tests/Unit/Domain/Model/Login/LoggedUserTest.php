<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Domain\Model\Login;

use Polsl\Domain\Model\Login\LoggedUser;
use Polsl\Domain\Model\User\Role;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class LoggedUserTest extends UnitTestCase
{
    public function test_user_has_to_contain_username(): void
    {
        // Expect
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Username cannot be empty.');

        // Act
        LoggedUser::create(
            Faker::intId(),
            '',
            [],
        );
    }

    public function test_role_user_is_added_by_default(): void
    {
        // Act
        $user = LoggedUser::create(
            Faker::intId(),
            Faker::email(),
            [],
        );

        // Assert
        self::assertCount(1, $user->roles());
        self::assertEquals(
            [
                Role::USER->value,
            ],
            $user->roles(),
        );
    }
}
