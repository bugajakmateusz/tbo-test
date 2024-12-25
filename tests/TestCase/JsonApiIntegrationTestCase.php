<?php

declare(strict_types=1);

namespace Polsl\Tests\TestCase;

use Polsl\Packages\JsonApi\Contracts\SchemaInterface;
use Polsl\Packages\TestCase\Client\KernelBrowserInterface;
use Polsl\Packages\TestCase\Fixtures\Entity\User;
use Polsl\Packages\TestCase\IntegrationTestCase;
use Polsl\Tests\TestCase\Application\Client\JsonApi\JsonApiClient;
use Polsl\Tests\TestCase\Application\Client\JsonApi\JsonApiDocument;
use Psr\Container\ContainerInterface;

abstract class JsonApiIntegrationTestCase extends IntegrationTestCase
{
    private const SERVICE_SCHEMAS_LOCATOR_ID = 'polsl.json_api.schemas_locator';

    /** @param class-string $type */
    public function jsonApiClient(
        string $type,
        ?KernelBrowserInterface $client = null,
    ): JsonApiClient {
        $schemasLocator = $this->schemasServiceLocator();
        /** @var SchemaInterface $schema */
        $schema = $schemasLocator->get($type);

        return new JsonApiClient(
            $schema,
            $client ?? $this->client(),
            $this->jsonSerializer(),
        );
    }

    /** @param class-string $type */
    public function loggedJsonApiClient(
        string $type,
        User $loggedUser,
        string $firewall = self::FIREWALL_MAIN,
    ): JsonApiClient {
        return $this->jsonApiClient(
            $type,
            $this->loggedClient(
                $loggedUser,
                $firewall,
            ),
        );
    }

    public function assertJsonApiType(string $expectedType, JsonApiDocument $jsonApiDocument): void
    {
        self::assertSame($expectedType, $jsonApiDocument->type());
    }

    /** @param array<int,int|string> $expectedIds */
    public function assertJsonApiIds(array $expectedIds, JsonApiDocument $jsonApiDocument): void
    {
        $documentIds = $jsonApiDocument->ids();

        self::assertEquals(
            $this->sortIds($expectedIds),
            $this->sortIds($documentIds),
        );
    }

    public function assertJsonApiItemsCount(int $expectedCount, JsonApiDocument $jsonApiDocument): void
    {
        self::assertSame($expectedCount, $jsonApiDocument->resourcesCount());
    }

    /** @param array<string,mixed> $expectedAttributes */
    protected function assertJsonApiAttributes(
        JsonApiDocument $jsonApiDocument,
        array $expectedAttributes,
    ): void {
        $actualAttributes = $jsonApiDocument->attributes();
        \ksort($actualAttributes);
        \ksort($expectedAttributes);

        self::assertSame($expectedAttributes, $actualAttributes);
    }

    /** @param array<string,mixed> $expectedAttributes */
    protected function assertJsonApiIncludeAttributes(
        string $id,
        string $type,
        JsonApiDocument $jsonApiDocument,
        array $expectedAttributes,
    ): void {
        self::assertTrue(
            $jsonApiDocument->hasInclude(
                $id,
                $type,
            ),
            "Include with id '{$id}' and type '{$type}' not found.",
        );
        $include = $jsonApiDocument->getInclude($id, $type);

        $this->assertJsonApiAttributes($include, $expectedAttributes);
    }

    /** @param array<string,null|mixed[]> $expectedRelationships */
    protected function assertJsonApiRelationships(JsonApiDocument $jsonApiDocument, array $expectedRelationships): void
    {
        self::assertSame(
            $this->sortRelationships(
                $expectedRelationships,
            ),
            $this->sortRelationships(
                $jsonApiDocument->relationshipsArray(),
            ),
        );
    }

    private function schemasServiceLocator(): ContainerInterface
    {
        /** @var ContainerInterface $schemasServiceLocator */
        $schemasServiceLocator = $this->container()
            ->get(self::SERVICE_SCHEMAS_LOCATOR_ID)
        ;

        return $schemasServiceLocator;
    }

    /**
     * @param array<int,int|string> $ids
     *
     * @return array<int,int|string>
     */
    private function sortIds(array $ids): array
    {
        \sort($ids);

        return $ids;
    }

    /**
     * @param array<int|string,mixed> $relationships
     *
     * @return array<int|string,mixed>
     */
    private function sortRelationships(array $relationships): array
    {
        \ksort($relationships);

        $firstItem = \reset($relationships);
        $sortByIds = \count($relationships) > 1
            && \array_is_list($relationships)
            && \is_array($firstItem)
            && \array_key_exists('id', $firstItem)
        ;
        if (true === $sortByIds) {
            \usort(
                $relationships,
                static fn (?array $left, ?array $right): int => ($left['id'] ?? 0) <=> ($right['id'] ?? 0),
            );
        }

        return \array_map(
            function (mixed $relationship): mixed {
                if (\is_array($relationship)) {
                    return $this->sortRelationships($relationship);
                }

                return $relationship;
            },
            $relationships,
        );
    }
}
