<?php

declare(strict_types=1);

$waitForDb = static function (
    string $dsn,
    string $user,
    string $password,
    int $retries = 30,
): void {
    $upAndRunning = false;
    $try = 1;

    do {
        if ($try > $retries) {
            throw new RuntimeException('Reached maximum retries count.');
        }

        try {
            new PDO(
                $dsn,
                $user,
                $password,
            );

            $upAndRunning = true;
        } catch (PDOException $exception) {
            echo "Connection failed for DSN: '{$dsn}', retry...", \PHP_EOL;

            \sleep(2);
        }

        ++$try;
    } while (!$upAndRunning);

    echo "Database '{$dsn}' is up and running", \PHP_EOL;
};

$host = $argv[1];
$database = $argv[2];
if (!isset($host, $database)) {
    throw new RuntimeException('Missing argument, usage: ./wait-for-db.php hostname database');
}

$waitForDb(
    "pgsql:dbname={$database};host={$host}",
    'root',
    'tab-admin',
);
