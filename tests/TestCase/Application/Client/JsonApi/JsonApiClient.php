<?php

declare(strict_types=1);

namespace Tab\Tests\TestCase\Application\Client\JsonApi;

use Tab\Packages\Constants\HttpMethods;
use Tab\Packages\JsonApi\Application\JsonApiKeywords;
use Tab\Packages\JsonApi\Application\Relationships;
use Tab\Packages\JsonApi\Contracts\SchemaInterface;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;
use Tab\Packages\TestCase\Client\KernelBrowserInterface;
use Tab\Packages\TestCase\Client\RequestInterface;

final readonly class JsonApiClient
{
    public function __construct(
        private SchemaInterface $schema,
        private KernelBrowserInterface $httpClient,
        private JsonSerializerInterface $jsonSerializer,
    ) {
    }

    /** @param array<string,array<string>|string> $additionalQueryParams */
    public function requestList(
        int $pageSize = 0,
        int $pageNumber = 0,
        array $additionalQueryParams = [],
    ): JsonApiResponse {
        $pageQueryParams = [];
        if ($pageSize > 0) {
            $pageQueryParams[JsonApiKeywords::PAGE][JsonApiKeywords::PAGE_SIZE] = $pageSize;
        }

        if ($pageNumber > 0) {
            $pageQueryParams[JsonApiKeywords::PAGE][JsonApiKeywords::PAGE_NUMBER] = $pageNumber;
        }

        $queryParams = \array_merge(
            $additionalQueryParams,
            $pageQueryParams,
        );

        $response = $this->httpClient
            ->request(
                RequestInterface::METHOD_GET,
                $this->generateUrl(),
                $queryParams,
            )
        ;

        return new JsonApiResponse($response, $this->jsonSerializer);
    }

    /** @param array<string,array<string>|string> $queryParams */
    public function requestResource(string $id, array $queryParams = []): JsonApiResponse
    {
        $response = $this->httpClient
            ->request(
                HttpMethods::GET,
                $this->generateUrl($id),
                $queryParams,
            )
        ;

        return new JsonApiResponse($response, $this->jsonSerializer);
    }

    /** @param array<string,mixed> $attributes */
    public function createResource(
        array $attributes = [],
        Relationships $relationships = null,
    ): JsonApiResponse {
        $resourceType = $this->schema
            ->resourceType()
        ;
        $relationships ??= Relationships::fromArray([]);
        $content = [
            JsonApiKeywords::DATA => [
                JsonApiKeywords::TYPE => $resourceType,
                JsonApiKeywords::ATTRIBUTES => $attributes,
                JsonApiKeywords::RELATIONSHIPS => $relationships->toArray(),
            ],
        ];

        $response = $this->httpClient
            ->request(
                RequestInterface::METHOD_POST,
                $this->generateUrl(),
                [],
                $this->jsonSerializer
                    ->encode($content),
            )
        ;

        return new JsonApiResponse($response, $this->jsonSerializer);
    }

    /**
     * @param array<string,mixed> $attributes
     * @param array<string,mixed> $relationships
     */
    public function updateResource(
        string $id,
        array $attributes = [],
        array $relationships = [],
    ): JsonApiResponse {
        $resourceType = $this->schema
            ->resourceType()
        ;
        $content = [
            JsonApiKeywords::DATA => [
                JsonApiKeywords::TYPE => $resourceType,
                JsonApiKeywords::ID => $id,
                JsonApiKeywords::ATTRIBUTES => $attributes,
                JsonApiKeywords::RELATIONSHIPS => $relationships,
            ],
        ];

        $response = $this->httpClient
            ->request(
                RequestInterface::METHOD_PATCH,
                $this->generateUrl($id),
                [],
                $this->jsonSerializer
                    ->encode($content),
            )
        ;

        return new JsonApiResponse($response, $this->jsonSerializer);
    }

    /**
     * @param list<
     *     array<string, mixed>
     * > $resources
     */
    public function updateRelationshipResource(
        string $id,
        string $relationshipName,
        array $resources,
    ): JsonApiResponse {
        $dataResourcesArray = [
            'data' => $resources,
        ];

        $response = $this->httpClient
            ->request(
                RequestInterface::METHOD_PATCH,
                $this->generateUrl($id, $relationshipName),
                [],
                $this->jsonSerializer
                    ->encode($dataResourcesArray),
            )
        ;

        return new JsonApiResponse($response, $this->jsonSerializer);
    }

    public function deleteRelationshipResource(
        string $id,
        string $relationshipName,
        string $relationshipId,
    ): JsonApiResponse {
        $response = $this->httpClient
            ->request(
                RequestInterface::METHOD_DELETE,
                $this->generateUrl($id, $relationshipName, $relationshipId),
            )
        ;

        return new JsonApiResponse($response, $this->jsonSerializer);
    }

    private function generateUrl(
        string $id = '',
        ?string $relationship = null,
        ?string $relationshipId = null,
    ): string {
        $segments = [
            '/api',
            'json-api',
        ];

        $segments[] = $this->schema
            ->resourceType()
        ;

        if ('' !== $id) {
            $segments[] = $id;
        }

        if (null !== $relationship) {
            $segments[] = 'relationships';
            $segments[] = $relationship;
        }

        if (null !== $relationshipId) {
            $segments[] = $relationshipId;
        }

        return \implode('/', $segments);
    }
}
