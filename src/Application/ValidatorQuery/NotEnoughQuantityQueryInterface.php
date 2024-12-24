<?php

declare(strict_types=1);

namespace Tab\Application\ValidatorQuery;

interface NotEnoughQuantityQueryInterface
{
    public function query(int $quantity, int $snackId): bool;
}
