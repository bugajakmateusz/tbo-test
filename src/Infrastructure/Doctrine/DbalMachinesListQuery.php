<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine;

use Tab\Application\Query\MachinesList\MachinesListQueryInterface;
use Tab\Application\Query\MachinesList\MachinesListView;
use Tab\Application\Schema\MachineSchema;
use Tab\Application\View\MachineView;
use Tab\Infrastructure\Doctrine\Fields\MachineFieldsFactory;
use Tab\Packages\Constants\Database\Tables;
use Tab\Packages\DbConnection\DbConnectionInterface;
use Tab\Packages\JsonApi\QueryTemplate\DbalListQueryTemplate;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\Filters;
use Tab\Packages\ResourcesList\Page;
use Tab\Packages\ResourcesList\TotalItems;
use Tab\Packages\SqlExpressions\JsonObject;

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
}
