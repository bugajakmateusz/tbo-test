<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

require \dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(\dirname(__DIR__) . '/.env');

$envKeepCache = $_SERVER['KEEP_CACHE'] ?? $_ENV['KEEP_CACHE'] ?? false;
$keepCache = \filter_var($envKeepCache, \FILTER_VALIDATE_BOOLEAN);

if (!$keepCache) {
    $filesystem = new Filesystem();
    $filesystem->remove(\dirname(__DIR__) . '/var/cache/test');
}
