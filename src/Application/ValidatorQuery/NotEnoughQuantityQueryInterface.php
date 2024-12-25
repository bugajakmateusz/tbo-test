<?php

declare(strict_types=1);

namespace Tab\Application\ValidatorQuery;

interface NotEnoughQuantityQueryInterface
{
    public function queryToAdd(int $quantity, int $snackId): bool;

    public function queryToUpdate(int $quantity, int $machineSnackId): bool;
}
