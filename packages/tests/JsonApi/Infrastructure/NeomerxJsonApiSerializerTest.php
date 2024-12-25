<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\JsonApi\Infrastructure;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Factories\Factory;
use Neomerx\JsonApi\Schema\BaseSchema;
use Neomerx\JsonApi\Schema\SchemaContainer;
use Polsl\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Polsl\Packages\JsonApi\Infrastructure\NeomerxJsonApiSerializer;
use Polsl\Packages\JsonSerializer\NativeJsonSerializer;
use Polsl\Packages\Tests\JsonApi\Contracts\AbstractJsonApiSerializerTestCase;

/** @internal */
final class NeomerxJsonApiSerializerTest extends AbstractJsonApiSerializerTestCase
{
    public function createSerializer(): JsonApiSerializerInterface
    {
        return new NeomerxJsonApiSerializer($this->createEncoder(), new NativeJsonSerializer());
    }

    private function createEncoder(): EncoderInterface
    {
        $factory = new Factory();
        $stdClassSchema = new class($factory) extends BaseSchema {
            public function getType(): string
            {
                return 'std';
            }

            public function getId($resource): ?string
            {
                return (string) ($resource->id ?? '-1');
            }

            /** @return iterable<string,string> */
            public function getAttributes($resource, ContextInterface $context): iterable
            {
                return [];
            }

            /** @return iterable<string,string> */
            public function getRelationships($resource, ContextInterface $context): iterable
            {
                return [];
            }

            /** @return iterable<string,string> */
            public function getLinks($resource): iterable
            {
                return [];
            }
        };
        $schemaContainer = new SchemaContainer($factory, [\stdClass::class => $stdClassSchema]);

        return new Encoder($factory, $schemaContainer);
    }
}
