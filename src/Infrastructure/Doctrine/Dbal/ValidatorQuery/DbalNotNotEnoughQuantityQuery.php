<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Doctrine\Dbal\ValidatorQuery;

use Tab\Application\ValidatorQuery\NotEnoughQuantityQueryInterface;
use Tab\Packages\DbConnection\DbConnectionInterface;

final readonly class DbalNotNotEnoughQuantityQuery implements NotEnoughQuantityQueryInterface
{
    public function __construct(private DbConnectionInterface $connection) {}

    public function queryToAdd(int $quantity, int $snackId): bool
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

    public function queryToUpdate(int $quantity, int $machineSnackId): bool
    {
        $statement = $this->connection
            ->fetchOne(
                <<<'SQL'
                    SELECT ws.quantity
                    FROM machine_snacks ms
                    INNER JOIN warehouse_snacks ws ON ws.snack_id = ms.snack_id
                    WHERE ms.id = :machine_snack_id

                    SQL,
                [
                    'machine_snack_id' => $machineSnackId,
                ],
                [
                    'machine_snack_id' => \PDO::PARAM_INT,
                ],
            )
        ;

        return $quantity > $statement;
    }
}
