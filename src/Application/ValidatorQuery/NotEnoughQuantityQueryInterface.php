<?php

declare(strict_types=1);

namespace Polsl\Application\ValidatorQuery;

interface NotEnoughQuantityQueryInterface
{
    public function queryToAdd(int $quantity, int $snackId): bool;

    public function queryToUpdate(int $quantity, int $machineSnackId): bool;
}
