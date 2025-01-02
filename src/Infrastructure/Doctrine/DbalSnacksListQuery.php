<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use Polsl\Application\Query\SnacksList\SnacksListHandler;
use Polsl\Application\Query\SnacksList\SnacksListQueryInterface;
use Polsl\Application\Query\SnacksList\SnacksListView;
use Polsl\Application\Schema\SnackSchema;
use Polsl\Application\View\SnackView;
use Polsl\Infrastructure\Doctrine\Fields\SnackFieldsFactory;
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

    protected function applyOrdering(QueryBuilder $queryBuilder, string $tableAlias): void
    {
        $queryBuilder
            ->orderBy("LOWER({$tableAlias}.name)")
        ;
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
