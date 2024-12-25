<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use Polsl\Application\Query\MachinesList\MachinesListHandler;
use Polsl\Application\Query\MachinesList\MachinesListQueryInterface;
use Polsl\Application\Query\MachinesList\MachinesListView;
use Polsl\Application\Schema\MachineSchema;
use Polsl\Application\View\MachineView;
use Polsl\Infrastructure\Doctrine\Fields\MachineFieldsFactory;
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

final class DbalMachinesListQuery extends DbalListQueryTemplate implements MachinesListQueryInterface
{
    public function __construct(
        private readonly JsonSerializerInterface $jsonSerializer,
        private readonly DbConnectionInterface $dbConnection,
        private readonly MachineFieldsFactory $machineFieldsFactory,
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
    ): MachinesListView {
        /**
         * @var array{
         *     totalItems: int,
         *     items: array<
         *         int,
         *         array{
         *             id?: int,
         *             location?: string,
         *             positions_no?: int,
         *             positions_capacity?: int,
         *         }
         *     >
         * } $machinesData
         */
        $machinesData = $this->listResources(
            $page,
            $fields,
            $filters,
        );
        $machinesView = \array_map(
            static fn (array $machineData): MachineView => MachineView::fromArray($machineData),
            $machinesData['items'],
        );
        $totalItems = TotalItems::fromInt($machinesData['totalItems']);

        return new MachinesListView(
            $totalItems,
            $fields,
            ...$machinesView,
        );
    }

    protected function resourceName(): string
    {
        return 'machines';
    }

    protected function idKey(): string
    {
        return Tables\Machines::FIELD_MACHINE_ID;
    }

    protected function tableName(): string
    {
        return Tables::MACHINES;
    }

    protected function dataFields(string $tableAlias, ?Fields $fields = null): JsonObject
    {
        return $this->machineFieldsFactory
            ->create(
                $tableAlias,
                $fields ?? Fields::createFieldsForType(MachineSchema::TYPE),
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
                MachinesListHandler::FILTER_ID => $this->applyIdFilter(
                    $queryBuilder,
                    $tableAlias,
                    $filter,
                ),
                default => throw new \RuntimeException("Filter '{$filter->name()}' is not supported."),
            };
        }
    }

    private function applyIdFilter(QueryBuilder $queryBuilder, string $tableAlias, Filter $filter): void
    {
        $queryBuilder
            ->andWhere("{$tableAlias}.machine_id = :id")
            ->setParameter(
                'id',
                $filter->intValue(),
                \PDO::PARAM_INT,
            )
        ;
    }
}
