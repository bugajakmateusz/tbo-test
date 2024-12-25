<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Application\UsersList;

use Polsl\Application\Exception\ApplicationException;
use Polsl\Application\Query\UsersList\UsersList;
use Polsl\Application\Query\UsersList\UsersListHandler;
use Polsl\Application\Query\UsersList\UsersListQueryInterface;
use Polsl\Application\Query\UsersList\UsersListView;
use Polsl\Domain\Model\Login\LoggedUser;
use Polsl\Domain\Model\User\Role;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\Filters;
use Polsl\Packages\ResourcesList\Page;
use Polsl\Packages\TestCase\UnitTestCase;
use Polsl\Tests\TestCase\Application\Mock\FakeLoggedUserService;

/**
 * @internal
 */
final class UsersListHandlerTest extends UnitTestCase
{
    public function test(): void
    {
        // Arrange
        $loggedUser = LoggedUser::create(
            Faker::intId(),
            Faker::email(),
            [Role::USER->value],
        );
        $handler = self::createHandler($loggedUser);
        $command = self::createQuery();
        $adminRole = Role::ADMIN->value;
        $officeManagerRole = Role::OFFICE_MANAGER->value;

        // Expect
        self::expectException(ApplicationException::class);
        self::expectExceptionMessage("Roles {$adminRole} or {$officeManagerRole} are required to fetch users list.");

        // Act
        $handler($command);
    }

    private static function createHandler(LoggedUser $loggedUser): UsersListHandler
    {
        $usersRepository = new class implements UsersListQueryInterface {
            public function query(Page $page, Filters $filters, Fields $fields): UsersListView
            {
                throw new \RuntimeException('Not implemented.');
            }
        };

        return new UsersListHandler(
            $usersRepository,
            new FakeLoggedUserService($loggedUser),
        );
    }

    private static function createQuery(): UsersList
    {
        return new UsersList(
            Filters::fromFilters(),
            Page::fromArray([]),
        );
    }
}
