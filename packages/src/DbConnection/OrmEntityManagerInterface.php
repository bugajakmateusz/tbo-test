<?php

declare(strict_types=1);

namespace Tab\Packages\DbConnection;

interface OrmEntityManagerInterface
{
    public function flush(): void;

    public function wrapInTransaction(\Closure $callable): mixed;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;

    public function clear(): void;
}
