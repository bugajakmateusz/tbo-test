<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Client;

use Tab\Packages\TestCase\Client\KernelBrowserInterface;

interface ClientInterface
{
    public function withHttpClient(KernelBrowserInterface $httpClient): self;
}
