<?php

declare(strict_types=1);

namespace Tab\Application\ValidatorQuery;

interface SnackExistsQueryInterface
{
    public function query(string $snack): bool;
}
