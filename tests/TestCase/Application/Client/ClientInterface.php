<?php

declare(strict_types=1);

namespace Polsl\Tests\TestCase\Application\Client;

use Polsl\Packages\TestCase\Client\KernelBrowserInterface;

interface ClientInterface
{
    public function withHttpClient(KernelBrowserInterface $httpClient): self;
}
