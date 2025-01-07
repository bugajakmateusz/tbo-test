<?php

declare(strict_types=1);

$path = \dirname(__DIR__) . '/var/cache/prod/Polsl_KernelProdContainer.preload.php';
if (\file_exists($path)) {
    // Temporary increase memory limit
    \ini_set('memory_limit', '160M');
    require $path;
}
