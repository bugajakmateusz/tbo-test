<?php

declare(strict_types=1);

namespace Tab\Application\Schema;

use Tab\Application\View\MachineSnackView;

final class MachineSnackSchema extends AbstractSchema
{
    public const TYPE = 'machine-snacks';
    public const ATTRIBUTE_QUANTITY = 'quantity';
    public const ATTRIBUTE_POSITION = 'position';
    public const ATTRIBUTE_PRICE = 'price';
    public const RELATIONSHIP_SNACK = 'snack';
    public const RELATIONSHIP_MACHINE = 'machine';

    public function resourceType(): string
    {
        return self::TYPE;
    }

    /** @param MachineSnackView $resource */
    public function attributes(object $resource): array
    {
        return [
            self::ATTRIBUTE_QUANTITY => $resource->quantity,
            self::ATTRIBUTE_POSITION => $resource->position,
            self::ATTRIBUTE_PRICE => $resource->price,
        ];
    }

    /** @param MachineSnackView $resource */
    public function relationships(object $resource): array
    {
        return [
            self::RELATIONSHIP_SNACK => $resource->snack,
        ];
    }
}
