<?php

declare(strict_types=1);

namespace Polsl\Infrastructure\Doctrine\Dbal\Reports;

use Polsl\Application\Query\MachineReport\MachineReportQueryInterface;
use Polsl\Application\View\MachineReportView;
use Polsl\Packages\DbConnection\DbConnectionInterface;

final class MachineReportQuery implements MachineReportQueryInterface
{
    public function __construct(private DbConnectionInterface $connection) {}

    public function get(array $machineIds): MachineReportView
    {
        /**
         * @var false|list<array{
         *     machine_id?: int|string|null,
         *     snack_id?: int|string|null,
         *     quantity?: int|string|null,
         *     snack_name?: string|null,
         * }> $data
         */
        $data = $this->connection
            ->fetchAllAssociative(
                <<<'SQL'
                    SELECT
                        ms.machine_id,
                        ms.snack_id,
                        ms.position,
                        CASE
                            WHEN ms.quantity > 0 THEN ms.quantity
                            ELSE (
                                SELECT quantity
                                FROM machine_snacks
                                WHERE machine_id = ms.machine_id
                                  AND position = ms.position
                                  AND quantity > 0
                                LIMIT 1
                            )
                            END AS quantity,
                        s.name as snack_name
                    FROM machine_snacks ms
                             JOIN snacks s ON ms.snack_id = s.snack_id
                    WHERE ms.machine_id IN (:machineIds)
                    ORDER BY ms.position;


                    SQL,
                [
                    'machineIds' => $machineIds,
                ],
                [
                    'machineIds' => DbConnectionInterface::PARAM_STR_ARRAY,
                ],
            )
        ;

        if (false === $data) {
            throw new \RuntimeException('Cannot fetch data.');
        }

        return MachineReportView::fromArray($data);
    }
}
