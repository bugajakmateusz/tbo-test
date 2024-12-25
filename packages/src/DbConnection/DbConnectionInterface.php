<?php

declare(strict_types=1);

namespace Polsl\Packages\DbConnection;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

interface DbConnectionInterface
{
    public const PARAM_STR_ARRAY = ArrayParameterType::STRING;
    public const PARAM_INT_ARRAY = ArrayParameterType::INTEGER;

    /**
     * Prepares and executes an SQL query and returns the result as an array of associative arrays.
     *
     * @param string                        $query  the SQL query
     * @param array<int|string, mixed>      $params the query parameters
     * @param array<int|string, int|string> $types  the query parameter types
     *
     * @return array<int,array<string,mixed>>
     */
    public function fetchAllAssociative(
        string $query,
        array $params = [],
        array $types = [],
    ): array;

    /**
     * Prepares and executes an SQL query and returns the first row of the result
     * as an associative array.
     *
     * @param string                        $query  the SQL query
     * @param array<int|string, mixed>      $params the prepared statement params
     * @param array<int|string, int|string> $types  the query parameter types
     *
     * @return array<string, mixed>|false false is returned if no rows are found
     */
    public function fetchAssociative(
        string $query,
        array $params = [],
        array $types = [],
    ): array|false;

    /**
     * Inserts a table row with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string         $table the expression of the table to insert data into, quoted or unquoted
     * @param mixed[]        $data  an associative array containing column-value pairs
     * @param int[]|string[] $types types of the inserted data
     *
     * @return int|string the number of affected rows
     */
    public function insert(
        string $table,
        array $data,
        array $types = [],
    ): int|string;

    /**
     * Executes an SQL UPDATE statement on a table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string         $table      the expression of the table to update quoted or unquoted
     * @param mixed[]        $data       an associative array containing column-value pairs
     * @param mixed[]        $identifier The update criteria. An associative array containing column-value pairs.
     * @param int[]|string[] $types      types of the merged $data and $identifier arrays in that order
     *
     * @return int|string the number of affected rows
     */
    public function update(
        string $table,
        array $data,
        array $identifier,
        array $types = [],
    ): int|string;

    /**
     * Prepares and executes an SQL query and returns the value of a single column
     * of the first row of the result.
     *
     * @param string                                                     $query  the SQL query to be executed
     * @param array<int|string, mixed>                                   $params the prepared statement params
     * @param array<int, null|int|string>|array<string, null|int|string> $types  Parameter types
     *
     * @return false|mixed false is returned if no rows are found
     */
    public function fetchOne(
        string $query,
        array $params = [],
        array $types = [],
    ): mixed;

    /**
     * Prepares and executes an SQL query and returns the result as an array of the first column values.
     *
     * @param string                        $query  the SQL query
     * @param array<int|string, mixed>      $params the query parameters
     * @param array<int|string, int|string> $types  the query parameter types
     *
     * @return array<int,mixed>
     */
    public function fetchFirstColumn(
        string $query,
        array $params = [],
        array $types = [],
    ): array;

    /**
     * Executes an SQL DELETE statement on a table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string         $table      the expression of the table on which to delete
     * @param mixed[]        $identifier The deletion criteria. An associative array containing column-value pairs.
     * @param int[]|string[] $types      the types of identifiers
     *
     * @return int|string the number of affected rows
     */
    public function delete(
        string $table,
        array $identifier,
        array $types = [],
    ): int|string;

    /**
     * Executes an SQL statement with the given parameters and returns the number of affected rows.
     *
     * Could be used for:
     *  - DML statements: INSERT, UPDATE, DELETE, etc.
     *  - DDL statements: CREATE, DROP, ALTER, etc.
     *  - DCL statements: GRANT, REVOKE, etc.
     *  - Session control statements: ALTER SESSION, SET, DECLARE, etc.
     *  - Other statements that don't yield a row set.
     *
     * This method supports PDO binding types as well as DBAL mapping types.
     *
     * @param string                                                     $sql    SQL statement
     * @param array<int, mixed>|array<string, mixed>                     $params Statement parameters
     * @param array<int, null|int|string>|array<string, null|int|string> $types  Parameter types
     *
     * @return int|string the number of affected rows
     */
    public function executeStatement(
        string $sql,
        array $params = [],
        array $types = [],
    ): int|string;

    /** Creates a new instance of a SQL query builder. */
    public function createQueryBuilder(): QueryBuilder;
}
