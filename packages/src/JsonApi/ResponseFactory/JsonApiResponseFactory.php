<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonApi\ResponseFactory;

use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\Includes;
use Polsl\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Polsl\Packages\Responder\Response\ResponseFactoryInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Polsl\Packages\Responder\Response\ResponseSpecification;

final readonly class JsonApiResponseFactory implements JsonApiResponseFactoryInterface
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function listResponse(
        array $objects,
        ?Includes $includes = null,
        ?array $meta = null,
        array $fieldSets = [],
        int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface {
        $jsonApiContent = $this->jsonApiSerializer
            ->encodeData(
                $objects,
                $meta,
                $includes,
                $fieldSets,
            )
        ;

        return $this->responseFactory
            ->jsonStringResponse(
                $jsonApiContent,
                $statusCode,
                $responseSpecification,
            )
        ;
    }

    public function resourceResponse(
        ?object $resource = null,
        ?Includes $includes = null,
        ?array $meta = null,
        array $fieldSets = [],
        int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface {
        $jsonApiContent = $this->jsonApiSerializer
            ->encodeData(
                $resource ?? [],
                $meta,
                $includes,
                $fieldSets,
            )
        ;

        return $this->responseFactory
            ->jsonStringResponse(
                $jsonApiContent,
                $statusCode,
                $responseSpecification,
            )
        ;
    }

    public function resourceIdentifiersResponse(
        array|object $data,
        int $statusCode = HttpStatusCodes::HTTP_OK,
    ): ResponseInterface {
        $jsonApiContent = $this->jsonApiSerializer
            ->encodeIdentifiers($data)
        ;

        return $this->responseFactory
            ->jsonStringResponse(
                $jsonApiContent,
                $statusCode,
            )
        ;
    }
}
