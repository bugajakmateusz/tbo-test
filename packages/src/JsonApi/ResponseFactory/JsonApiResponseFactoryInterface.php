<?php

declare(strict_types=1);

namespace Polsl\Packages\JsonApi\ResponseFactory;

use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\Includes;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Polsl\Packages\Responder\Response\ResponseSpecification;

interface JsonApiResponseFactoryInterface
{
    /**
     * @param object[]                    $objects
     * @param array<string,array<string>> $fieldSets
     * @param null|array<mixed,mixed>     $meta
     */
    public function listResponse(
        array $objects,
        ?Includes $includes = null,
        ?array $meta = null,
        array $fieldSets = [],
        int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface;

    /**
     * @param array<string,array<string>> $fieldSets
     * @param null|array<mixed,mixed>     $meta
     */
    public function resourceResponse(
        ?object $resource = null,
        ?Includes $includes = null,
        ?array $meta = null,
        array $fieldSets = [],
        int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface;

    /** @param object|object[] $data */
    public function resourceIdentifiersResponse(
        array|object $data,
        int $statusCode = HttpStatusCodes::HTTP_OK,
    ): ResponseInterface;
}
