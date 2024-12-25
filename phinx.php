<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$_ENV['PASS_SALT'] = $_SERVER['PASSWORD_SALT'] ?? '';

$doctrineConnection = DriverManager::getConnection(
    [
        'url' => $_SERVER['DATABASE_URL'] ?? '',
        'charset' => 'UTF8',
    ],
);
$connection = $doctrineConnection->getNativeConnection();

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'env',
        'env' => [
            'name' => '%%PHINX_DB_NAME%%',
            'connection' => $connection,
        ],
    ],
    'version_order' => 'creation',
];
