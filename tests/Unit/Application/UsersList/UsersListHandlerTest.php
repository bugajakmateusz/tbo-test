<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Application\UsersList;

use Tab\Application\Exception\ApplicationException;
use Tab\Application\Query\UsersList\UsersList;
use Tab\Application\Query\UsersList\UsersListHandler;
use Tab\Application\Query\UsersList\UsersListQueryInterface;
use Tab\Application\Query\UsersList\UsersListView;
use Tab\Domain\Model\Login\LoggedUser;
use Tab\Domain\Model\User\Role;
use Tab\Packages\Faker\Faker;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\Filters;
use Tab\Packages\ResourcesList\Page;
use Tab\Packages\TestCase\UnitTestCase;
use Tab\Tests\TestCase\Application\Mock\FakeLoggedUserService;

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
        $usersRepository = new class() implements UsersListQueryInterface {
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
