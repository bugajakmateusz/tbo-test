<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use Tab\Application\Query\SnacksList\SnacksListHandler;
use Tab\Application\Query\SnacksList\SnacksListQueryInterface;
use Tab\Application\Query\SnacksList\SnacksListView;
use Tab\Application\Schema\SnackSchema;
use Tab\Application\View\SnackView;
use Tab\Infrastructure\Doctrine\Fields\SnackFieldsFactory;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\DbConnection\DbConnectionInterface;
use Tab\Packages\JsonApi\QueryTemplate\DbalListQueryTemplate;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\Filter;
use Tab\Packages\ResourcesList\Filters;
use Tab\Packages\ResourcesList\Page;
use Tab\Packages\ResourcesList\TotalItems;
use Tab\Packages\SqlExpressions\JsonObject;

final class DbalSnacksListQuery extends DbalListQueryTemplate implements SnacksListQueryInterface
{
    public function __construct(
        private readonly JsonSerializerInterface $jsonSerializer,
        private readonly DbConnectionInterface $dbConnection,
        private readonly SnackFieldsFactory $snackFieldsFactory,
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
    ): SnacksListView {
        /**
         * @var array{
         *     totalItems: int,
         *     items: array<
         *         int,
         *         array{
         *             id?: int,
         *             name?: string,
         *         }
         *     >
         * } $snacksData
         */
        $snacksData = $this->listResources(
            $page,
            $fields,
            $filters,
        );
        $snacksView = \array_map(
            static fn (array $snackData): SnackView => SnackView::fromArray(
                $snackData,
            ),
            $snacksData['items'],
        );
        $totalItems = TotalItems::fromInt(
            $snacksData['totalItems'],
        );

        return new SnacksListView(
            $totalItems,
            $fields,
            ...$snacksView,
        );
    }

    protected function resourceName(): string
    {
        return 'snacks';
    }

    protected function idKey(): string
    {
        return Tables\Snacks::FIELD_SNACK_ID;
    }

    protected function tableName(): string
    {
        return Tables::SNACKS;
    }

    protected function dataFields(
        string $tableAlias,
        ?Fields $fields = null,
    ): JsonObject {
        return $this->snackFieldsFactory
            ->create(
                $tableAlias,
                $fields ?? Fields::createFieldsForType(
                    SnackSchema::TYPE,
                ),
            )
        ;
    }

    protected function applyFilters(
        QueryBuilder $queryBuilder,
        string $tableAlias,
        ?Filters $filters = null,
    ): void {
        if (null === $filters) {
            return;
        }

        foreach ($filters->nonEmptyFilters() as $filter) {
            match ($filter->name()) {
                SnacksListHandler::FILTER_NAME => $this->applyNameFilter(
                    $queryBuilder,
                    $tableAlias,
                    $filter,
                ),
                default => throw new \RuntimeException("Filter '{$filter->name()}' is not supported."),
            };
        }
    }

    private function applyNameFilter(QueryBuilder $queryBuilder, string $tableAlias, Filter $filter): void
    {
        $queryBuilder
            ->andWhere("{$tableAlias}.name like :name")
            ->setParameter(
                'name',
                "%{$filter->stringValue()}%",
                \PDO::PARAM_STR,
            )
        ;
    }
}
