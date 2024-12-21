<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Client\JsonApi;

use Tab\Packages\HttpResponse\ResponseInterface;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;

final class JsonApiResponse
{
    public function __construct(
        public readonly ResponseInterface $response,
        private readonly JsonSerializerInterface $jsonSerializer,
    ) {}

    public function statusCode(): int
    {
        return $this->response
            ->statusCode()
        ;
    }

    public function isOk(): bool
    {
        return $this->response
            ->isOk()
        ;
    }

    public function document(): JsonApiDocument
    {
        /** @var \stdClass $document */
        $document = $this->jsonSerializer
            ->decode(
                $this->response
                    ->content(),
            )
        ;

        return new JsonApiDocument($document);
    }
}
