<?php

declare(strict_types=1);

namespace Tab\Application\Schema;

use Tab\Application\View\MachineView;

final class MachineSchema extends AbstractSchema
{
    public const TYPE = 'machines';
    public const ATTRIBUTE_LOCATION = 'location';
    public const ATTRIBUTE_POSITIONS_NUMBER = 'positionsNumber';
    public const ATTRIBUTE_POSITIONS_CAPACITY = 'positionsCapacity';
    public const RELATIONSHIP_MACHINE_SNACKS = 'machineSnacks';

    public function resourceType(): string
    {
        return self::TYPE;
    }

    /** @param MachineView $resource */
    public function attributes(object $resource): array
    {
        return [
            self::ATTRIBUTE_LOCATION => $resource->location,
            self::ATTRIBUTE_POSITIONS_NUMBER => $resource->positionsNo,
            self::ATTRIBUTE_POSITIONS_CAPACITY => $resource->positionsCapacity,
        ];
    }

    /** @param MachineView $resource */
    public function relationships(object $resource): array
    {
        return [
            self::RELATIONSHIP_MACHINE_SNACKS => $resource->machineSnacks,
        ];
    }
}
