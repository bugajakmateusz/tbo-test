<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Tab\CacheKernel;
use Tab\Kernel;
use Tab\Packages\Constants\Ip;

require \dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(\dirname(__DIR__) . '/.env');

if ((bool) $_SERVER['APP_DEBUG']) {
    \umask(0000);

    Debug::enable();
}

Request::setTrustedProxies(
    [
        Ip::DOCKER_SUBNET, // Docker's internal network
    ],
    Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_PORT,
);

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
// Wrap the default Kernel with the CacheKernel one in 'prod' environment
if (Kernel::ENV_PROD === $kernel->getEnvironment()) {
    $kernel = CacheKernel::create($kernel);
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
