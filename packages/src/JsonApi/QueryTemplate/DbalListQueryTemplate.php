<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonApi\QueryTemplate;

use Doctrine\DBAL\Query\QueryBuilder;
use Polsl\Packages\DbConnection\DbConnectionInterface;
use Polsl\Packages\JsonSerializer\JsonSerializerInterface;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\Filters;
use Polsl\Packages\ResourcesList\Page;
use Polsl\Packages\SqlExpressions\JsonArrayAggregate;
use Polsl\Packages\SqlExpressions\JsonObject;

abstract class DbalListQueryTemplate
{
    private const COUNT_KEY = 'entities_count';
    private const DATA_KEY = 'entities_data';

    private static ?string $resourceNameSlug = null;

    public function __construct(
        private readonly DbConnectionInterface $connection,
        private readonly JsonSerializerInterface $jsonSerializer,
    ) {}

    abstract protected function resourceName(): string;

    abstract protected function idKey(): string;

    abstract protected function tableName(): string;

    abstract protected function dataFields(string $tableAlias, ?Fields $fields = null): JsonObject;

    protected function resourceNameSlug(): string
    {
        return self::$resourceNameSlug ??= \str_replace(
            ['-', ' '],
            '_',
            $this->resourceName(),
        );
    }

    /** @return array{totalItems: int, items: array<mixed[]>} */
    protected function listResources(
        Page $page,
        ?Fields $fields = null,
        ?Filters $filters = null,
    ): array {
        $resourceNameSlug = $this->resourceNameSlug();
        $countQueryBuilder = $this->countQueryBuilder($filters);
        $dataQuery = $this->dataQuerySql(
            $page,
            $fields,
            $filters,
        );

        $rawDataKey = "{$resourceNameSlug}_raw_data";
        $listFields = new JsonObject();
        $listFields->addField(self::COUNT_KEY, "({$countQueryBuilder->getSQL()})");
        $listFields->addField(self::DATA_KEY, "({$dataQuery})");
        $mainQuery = "SELECT {$listFields->toString()} AS {$rawDataKey}";
        /** @var array<string,int|string> $parameterTypes */
        $parameterTypes = $countQueryBuilder->getParameterTypes();
        /** @var false|string $rawDatabaseData */
        $rawDatabaseData = $this->connection
            ->fetchOne(
                $mainQuery,
                $countQueryBuilder->getParameters(),
                $parameterTypes,
            )
        ;

        if (false === $rawDatabaseData) {
            throw new ListQueryException("Unable to fetch {$this->resourceName()} data.");
        }

        /**
         * @var array{
         *     entities_count?: int,
         *     entities_data?: array<
         *         mixed[],
         *     >,
         * } $databaseData
         */
        $databaseData = $this->jsonSerializer
            ->decode($rawDatabaseData, true)
        ;

        return [
            'totalItems' => $databaseData[self::COUNT_KEY] ?? 0,
            'items' => $databaseData[self::DATA_KEY] ?? [],
        ];
    }

    protected function countQueryBuilder(?Filters $filters = null): QueryBuilder
    {
        $tableAlias = "count_{$this->resourceNameSlug()}";
        $countQueryBuilder = $this->connection
            ->createQueryBuilder()
        ;
        $countQueryBuilder
            ->select("COUNT({$tableAlias}.{$this->idKey()})")
            ->from($this->tableName(), $tableAlias)
        ;
        $this->applyFilters(
            $countQueryBuilder,
            $tableAlias,
            $filters,
        );

        return $countQueryBuilder;
    }

    protected function dataQuerySql(
        Page $page,
        ?Fields $fields = null,
        ?Filters $filters = null,
    ): string {
        $innerTableAlias = "inner_{$this->resourceNameSlug()}";
        $innerDataQueryBuilder = $this->connection
            ->createQueryBuilder()
        ;
        $innerDataQueryBuilder
            ->select('*')
            ->from($this->tableName(), $innerTableAlias)
            ->setMaxResults($page->size())
            ->setFirstResult($page->offset())
        ;
        $this->applyFilters(
            $innerDataQueryBuilder,
            $innerTableAlias,
            $filters,
        );
        $this->applyOrdering($innerDataQueryBuilder, $innerTableAlias);

        $dataTableAlias = "data_{$this->resourceNameSlug()}";
        $dataFields = $this->dataFields($dataTableAlias, $fields);
        $listDataAggregate = new JsonArrayAggregate($dataFields->toString());

        return "
            SELECT {$listDataAggregate->toString()}
            FROM ({$innerDataQueryBuilder->getSQL()}) AS {$dataTableAlias}
        ";
    }

    protected function applyFilters(
        QueryBuilder $queryBuilder,
        string $tableAlias,
        ?Filters $filters = null,
    ): void {}

    protected function applyOrdering(QueryBuilder $queryBuilder, string $tableAlias): void {}

    protected function generateFieldValue(string $tableAlias, string $value): string
    {
        return "{$tableAlias}.{$value}";
    }
}
