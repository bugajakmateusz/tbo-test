<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Dotenv\Dotenv;
use Tab\Kernel;

require \dirname(__DIR__, 2) . '/vendor/autoload.php';
(new Dotenv())->bootEnv(\dirname(__DIR__, 2) . '/.env');
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

return new Application($kernel);
