<?php

declare(strict_types=1);

namespace Polsl\Application\Query\MachinesList;

use Polsl\Application\Schema\MachineSchema;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\Filters;

final readonly class MachinesListHandler
{
    public const FILTER_ID = 'id';
    private const SUPPORTED_FILTERS = [
        self::FILTER_ID,
    ];

    public function __construct(
        private MachinesListQueryInterface $machinesListQuery,
    ) {}

    public function __invoke(MachinesList $machinesList): MachinesListView
    {
        $filters = $machinesList->filters;
        $filters->checkSupportedFilters(...self::SUPPORTED_FILTERS);
        $nonEmptyFilters = Filters::fromFilters(...$filters->nonEmptyFilters());
        $fields = $this->resolveFields($machinesList->fields);

        $machines = $this->machinesListQuery
            ->query(
                $machinesList->page,
                $nonEmptyFilters,
                $fields,
            )
        ;

        return $machines;
    }

    private function resolveFields(
        ?Fields $overwriteFields = null,
    ): Fields {
        if (null !== $overwriteFields && false === $overwriteFields->isEmpty()) {
            return $overwriteFields;
        }

        $machinesFields = Fields\TypeFields::create(
            MachineSchema::TYPE,
            [
                MachineSchema::ATTRIBUTE_LOCATION,
            ],
        );

        return Fields::createFromArray(
            [
                $machinesFields,
            ],
        );
    }
}
