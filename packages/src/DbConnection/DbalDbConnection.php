<?php

declare(strict_types=1);

namespace Polsl\Packages\DbConnection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final readonly class DbalDbConnection implements DbConnectionInterface
{
    public function __construct(private Connection $connection) {}

    public function fetchAllAssociative(
        string $query,
        array $params = [],
        array $types = [],
    ): array {
        return $this->connection
            ->fetchAllAssociative(
                $query,
                $params,
                $types,
            )
        ;
    }

    public function fetchAssociative(
        string $query,
        array $params = [],
        array $types = [],
    ): array|false {
        return $this->connection
            ->fetchAssociative(
                $query,
                $params,
                $types,
            )
        ;
    }

    public function insert(
        string $table,
        array $data,
        array $types = [],
    ): int|string {
        return $this->connection
            ->insert(
                $table,
                $data,
                $types,
            )
        ;
    }

    public function update(
        string $table,
        array $data,
        array $identifier,
        array $types = [],
    ): int|string {
        return $this->connection
            ->update(
                $table,
                $data,
                $identifier,
                $types,
            )
        ;
    }

    public function fetchOne(
        string $query,
        array $params = [],
        array $types = [],
    ): mixed {
        return $this->connection
            ->fetchOne(
                $query,
                $params,
                $types,
            )
        ;
    }

    public function fetchFirstColumn(
        string $query,
        array $params = [],
        array $types = [],
    ): array {
        return $this->connection
            ->fetchFirstColumn(
                $query,
                $params,
                $types,
            )
        ;
    }

    public function delete(
        string $table,
        array $identifier,
        array $types = [],
    ): int|string {
        return $this->connection
            ->delete(
                $table,
                $identifier,
                $types,
            )
        ;
    }

    public function executeStatement(
        string $sql,
        array $params = [],
        array $types = [],
    ): int|string {
        return $this->connection
            ->executeStatement(
                $sql,
                $params,
                $types,
            )
        ;
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->connection
            ->createQueryBuilder()
        ;
    }
}
