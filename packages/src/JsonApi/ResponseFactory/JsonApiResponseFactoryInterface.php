<?php

declare(strict_types=1);

namespace Tab\Packages\JsonApi\ResponseFactory;

use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonApi\Application\Includes;
use Tab\Packages\Responder\Response\ResponseInterface;
use Tab\Packages\Responder\Response\ResponseSpecification;

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
        object|array $data,
        int $statusCode = HttpStatusCodes::HTTP_OK,
    ): ResponseInterface;
}
