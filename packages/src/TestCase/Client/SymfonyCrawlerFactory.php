<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Client;

use Polsl\Packages\HttpResponse\ResponseInterface;

final class SymfonyCrawlerFactory implements CrawlerFactoryInterface
{
    public function createFromResponse(ResponseInterface $response): CrawlerInterface
    {
        return SymfonyCrawler::fromData(
            $response->uri(),
            $response->content(),
            $response->contentType(),
        );
    }

    public function createFromData(
        string $uri,
        string $content,
        string $contentType,
    ): CrawlerInterface {
        return SymfonyCrawler::fromData(
            $uri,
            $content,
            $contentType,
        );
    }
}
