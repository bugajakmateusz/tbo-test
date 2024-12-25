<?php

declare(strict_types=1);

namespace Polsl\Packages\Tests\JsonApi\Application;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\JsonApi\Application\Exception\ResourceException;
use Polsl\Packages\JsonApi\Application\Exception\ResourceIdentifierException;
use Polsl\Packages\JsonApi\Application\JsonApiKeywords;
use Polsl\Packages\JsonApi\Application\ResourceIdentifier;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class ResourceIdentifierTest extends UnitTestCase
{
    public const CONSTRUCTOR_ARRAY = 'array';
    public const CONSTRUCTOR_SCALARS = 'scalars';

    /**
     * @param \Closure(): string $constructorGenerator
     *
     * @dataProvider namedConstructorProviders
     */
    public function test_resource_identifiers_type_cannot_be_empty(\Closure $constructorGenerator): void
    {
        // Expect
        $this->expectException(ResourceIdentifierException::class);
        $this->expectExceptionMessage("Non-empty 'type' is required.");

        // Act
        $this->createResourceIdentifier(
            '',
            '123',
            $constructorGenerator(),
        );
    }

    /**
     * @param \Closure(): string $constructorGenerator
     *
     * @dataProvider namedConstructorProviders
     */
    public function test_resource_identifiers_id_cannot_be_empty(\Closure $constructorGenerator): void
    {
        // Arrange
        $constructor = $constructorGenerator();

        // Expect
        $this->expectException(ResourceIdentifierException::class);
        $this->expectExceptionMessage("Non-empty 'id' is required.");

        // Act
        $this->createResourceIdentifier(
            'tests',
            '',
            $constructor,
        );
    }

    /**
     * @param \Closure(): string $constructorGenerator
     *
     * @dataProvider namedConstructorProviders
     */
    public function test_resource_identifier_can_be_created(\Closure $constructorGenerator): void
    {
        // Arrange
        $constructor = $constructorGenerator();
        $type = 'tests';
        $id = '64223';

        // Act
        $resourceId = $this->createResourceIdentifier(
            $type,
            $id,
            $constructor,
        );

        // Assert
        self::assertSame($type, $resourceId->type());
        self::assertSame($id, $resourceId->id());
    }

    public function test_check_expected_type_fails_on_wrong_data(): void
    {
        // Arrange
        $testedType = Faker::word();
        $type = Faker::word() . Faker::intId();
        $resource = $this->createResourceIdentifier($type, Faker::stringNumberId());

        // Expect
        $this->expectException(ResourceException::class);
        $this->expectExceptionMessage("Expected type '{$testedType}', got '{$type}'.");

        // Act
        $resource->checkExpectedType($testedType);
    }

    public function test_check_expected_type_do_nothing_on_valid_data(): void
    {
        // Arrange
        $type = Faker::word();
        $resource = $this->createResourceIdentifier($type, Faker::stringNumberId());

        // Expect
        $this->expectNotToPerformAssertions();

        // Act
        $resource->checkExpectedType($type);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function namedConstructorProviders(): iterable
    {
        yield 'fromArray' => [
            static fn (): string => self::CONSTRUCTOR_ARRAY,
        ];
        yield 'fromScalars' => [
            static fn (): string => self::CONSTRUCTOR_SCALARS,
        ];
    }

    private function createResourceIdentifier(
        string $type = '',
        string $id = '',
        string $constructor = self::CONSTRUCTOR_ARRAY,
    ): ResourceIdentifier {
        return match ($constructor) {
            self::CONSTRUCTOR_ARRAY => ResourceIdentifier::fromArray(
                [
                    JsonApiKeywords::TYPE => $type,
                    JsonApiKeywords::ID => $id,
                ],
            ),
            self::CONSTRUCTOR_SCALARS => ResourceIdentifier::fromScalars($id, $type),
            default => throw new \RuntimeException("Named constructor '{$constructor}' is not supported."),
        };
    }
}
