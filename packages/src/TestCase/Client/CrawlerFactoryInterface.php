<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Client;

use Tab\Packages\HttpResponse\ResponseInterface;

interface CrawlerFactoryInterface
{
    public function createFromResponse(ResponseInterface $response): CrawlerInterface;

    public function createFromData(
        string $uri,
        string $content,
        string $contentType,
    ): CrawlerInterface;
}
