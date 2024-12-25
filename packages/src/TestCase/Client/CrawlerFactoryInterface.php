<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Client;

use Polsl\Packages\HttpResponse\ResponseInterface;

interface CrawlerFactoryInterface
{
    public function createFromResponse(ResponseInterface $response): CrawlerInterface;

    public function createFromData(
        string $uri,
        string $content,
        string $contentType,
    ): CrawlerInterface;
}
