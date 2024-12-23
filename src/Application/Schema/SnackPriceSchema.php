<?php

declare(strict_types=1);

namespace Tab\Application\Schema;

use Tab\Application\View\PriceView;

final class SnackPriceSchema extends AbstractSchema
{
    public const TYPE = 'snacks-prices';
    public const ATTRIBUTE_PRICE = 'price';
    public const RELATIONSHIP_SNACK = 'snack';
    public const RELATIONSHIP_MACHINE = 'machine';

    public function resourceType(): string
    {
        return self::TYPE;
    }

    /** @param PriceView $resource */
    public function attributes(object $resource): array
    {
        return [
            self::ATTRIBUTE_PRICE => $resource->price,
        ];
    }

    /** @param PriceView $resource */
    public function relationships(object $resource): array
    {
        return [
            self::RELATIONSHIP_SNACK => $resource->snack,
        ];
    }
}
