<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Dbal\ValidatorQuery;

use Tab\Application\ValidatorQuery\SnackExistsQueryInterface;
use Tab\Packages\DbConnection\DbConnectionInterface;

final readonly class DbalSnackExistsQuery implements SnackExistsQueryInterface
{
    public function __construct(private DbConnectionInterface $connection) {}

    public function query(string $snack): bool
    {
        $statement = $this->connection
            ->fetchAssociative(
                <<<'SQL'
                    SELECT snack_id
                    FROM snacks
                    WHERE name = :snack
                    LIMIT 1

                    SQL,
                ['snack' => $snack],
            )
        ;

        return false !== $statement;
    }
}
