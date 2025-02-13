<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine\Dbal\ValidatorQuery;

use Polsl\Application\ValidatorQuery\MachinePositionTakenQueryInterface;
use Polsl\Packages\DbConnection\DbConnectionInterface;

final readonly class DbalMachinePositionTakenQuery implements MachinePositionTakenQueryInterface
{
    public function __construct(private DbConnectionInterface $connection) {}

    public function query(string $position, int $machineId): bool
    {
        $statement = $this->connection
            ->fetchAssociative(
                <<<'SQL'
                    SELECT id
                    FROM machine_snacks
                    WHERE position = :position
                      AND quantity > 0
                      AND machine_id = :machineId

                    SQL,
                [
                    'position' => $position,
                    'machineId' => $machineId,
                ],
                [
                    'position' => \PDO::PARAM_STR,
                    'machineId' => \PDO::PARAM_INT,
                ],
            )
        ;

        return false !== $statement;
    }
}
