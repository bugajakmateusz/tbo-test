<?php

declare(strict_types=1);

namespace Tab\Application\Schema;

use Tab\Application\View\BuyView;

final class SnackBuySchema extends AbstractSchema
{
    public const TYPE = 'buys';
    public const ATTRIBUTE_PRICE = 'price';
    public const ATTRIBUTE_QUANTITY = 'quantity';
    public const RELATIONSHIP_SNACK = 'snack';

    public function resourceType(): string
    {
        return self::TYPE;
    }

    /** @param BuyView $resource */
    public function attributes(object $resource): array
    {
        return [
            self::ATTRIBUTE_PRICE => $resource->price,
            self::ATTRIBUTE_QUANTITY => $resource->quantity,
        ];
    }

    /** @param BuyView $resource */
    public function relationships(object $resource): array
    {
        return [
            self::RELATIONSHIP_SNACK => $resource->snack,
        ];
    }
}
