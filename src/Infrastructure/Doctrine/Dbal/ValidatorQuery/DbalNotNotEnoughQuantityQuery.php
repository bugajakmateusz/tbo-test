<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Dbal\ValidatorQuery;

use Tab\Application\ValidatorQuery\NotEnoughQuantityQueryInterface;
use Tab\Packages\DbConnection\DbConnectionInterface;

final readonly class DbalNotNotEnoughQuantityQuery implements NotEnoughQuantityQueryInterface
{
    public function __construct(private DbConnectionInterface $connection) {}

    public function query(int $quantity, int $snackId): bool
    {
        $statement = $this->connection
            ->fetchOne(
                <<<'SQL'
                    SELECT quantity
                    FROM warehouse_snacks
                    WHERE snack_id = :snack_id

                    SQL,
                [
                    'snack_id' => $snackId,
                ],
                [
                    'snack_id' => \PDO::PARAM_INT,
                ],
            )
        ;

        return $quantity > $statement;
    }
}
