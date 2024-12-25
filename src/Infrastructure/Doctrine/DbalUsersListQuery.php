<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use Polsl\Application\Query\UsersList\UsersListHandler;
use Polsl\Application\Query\UsersList\UsersListQueryInterface;
use Polsl\Application\Query\UsersList\UsersListView;
use Polsl\Application\Schema\UserSchema;
use Polsl\Application\View\UserView;
use Polsl\Infrastructure\Doctrine\Fields\UserFieldsFactory;
use Polsl\Packages\Constants\Database\Tables;
use Polsl\Packages\DbConnection\DbConnectionInterface;
use Polsl\Packages\JsonApi\QueryTemplate\DbalListQueryTemplate;
use Polsl\Packages\JsonSerializer\JsonSerializerInterface;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\Filter;
use Polsl\Packages\ResourcesList\Filters;
use Polsl\Packages\ResourcesList\Page;
use Polsl\Packages\ResourcesList\TotalItems;
use Polsl\Packages\SqlExpressions\JsonObject;
use Polsl\Packages\SqlExpressions\OrderBy;

final class DbalUsersListQuery extends DbalListQueryTemplate implements UsersListQueryInterface
{
    public function __construct(
        private readonly JsonSerializerInterface $jsonSerializer,
        private readonly DbConnectionInterface $dbConnection,
        private readonly UserFieldsFactory $userFieldsFactory,
    ) {
        parent::__construct(
            $this->dbConnection,
            $this->jsonSerializer,
        );
    }

    public function query(
        Page $page,
        Filters $filters,
        Fields $fields,
    ): UsersListView {
        /**
         * @var array{
         *     totalItems: int,
         *     items: array<
         *         int,
         *         array{
         *             id?: int,
         *             email?: string,
         *             name?: string,
         *             surname?: string,
         *             roles?: string[],
         *         }
         *     >
         * } $usersData
         */
        $usersData = $this->listResources(
            $page,
            $fields,
            $filters,
        );
        $usersViews = \array_map(
            static fn (array $userData): UserView => UserView::fromArray($userData),
            $usersData['items'],
        );
        $totalItems = TotalItems::fromInt($usersData['totalItems']);

        return new UsersListView(
            $totalItems,
            $fields,
            ...$usersViews,
        );
    }

    protected function resourceName(): string
    {
        return 'users';
    }

    protected function idKey(): string
    {
        return Tables\Users::FIELD_USER_ID;
    }

    protected function tableName(): string
    {
        return Tables::USERS;
    }

    protected function dataFields(string $tableAlias, ?Fields $fields = null): JsonObject
    {
        return $this->userFieldsFactory
            ->create(
                $tableAlias,
                $fields ?? Fields::createFieldsForType(UserSchema::TYPE),
            )
        ;
    }

    protected function applyFilters(QueryBuilder $queryBuilder, string $tableAlias, ?Filters $filters = null): void
    {
        if (null === $filters) {
            return;
        }
        foreach ($filters->nonEmptyFilters() as $filter) {
            match ($filter->name()) {
                UsersListHandler::FILTER_ID => $this->applyFilterId(
                    $tableAlias,
                    $queryBuilder,
                    $filter,
                ),
                UsersListHandler::FILTER_ME => null,
                default => throw new \RuntimeException("Filter '{$filter->name()}' is not implemented."),
            };
        }
    }

    protected function applyOrdering(QueryBuilder $queryBuilder, string $tableAlias): void
    {
        $queryBuilder->orderBy(
            "{$tableAlias}.user_id",
            OrderBy::DIRECTION_ASC,
        );
    }

    private function applyFilterId(
        string $tableAlias,
        QueryBuilder $queryBuilder,
        Filter $filter,
    ): void {
        $queryBuilder
            ->andWhere("{$tableAlias}.user_id = :userId")
            ->setParameter('userId', $filter->intValue())
        ;
    }
}
