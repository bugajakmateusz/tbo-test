<?php

declare(strict_types=1);

namespace Polsl\Application\ValidatorQuery;

interface EmailExistsQueryInterface
{
    public function query(string $email): bool;
}
