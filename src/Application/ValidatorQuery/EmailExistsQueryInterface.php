<?php

declare(strict_types=1);

namespace Tab\Application\ValidatorQuery;

interface EmailExistsQueryInterface
{
    public function query(string $email): bool;
}
