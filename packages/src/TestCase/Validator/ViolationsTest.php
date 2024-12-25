<?php

declare(strict_types=1);

namespace Polsl\Packages\TestCase\Validator;

use Polsl\Packages\TestCase\UnitTestCase;
use Polsl\Packages\Validator\ViolationInterface;
use Polsl\Packages\Validator\Violations;

/** @internal */
final class ViolationsTest extends UnitTestCase
{
    /**
     * @param \Closure(): array{
     *     violations: Violations,
     *     expectedIsEmpty: bool,
     * } $paramsGenerator
     *
     * @dataProvider emptyViolationsProvider
     */
    public function test_empty_violations_list(\Closure $paramsGenerator): void
    {
        // Arrange
        [
            'violations' => $violations,
            'expectedIsEmpty' => $expectedIsEmpty,
        ] = $paramsGenerator();

        // Act
        $isEmpty = $violations->isEmpty();

        // Assert
        self::assertSame($isEmpty, $expectedIsEmpty);
    }

    public function test_violations_list_can_be_converted_to_array(): void
    {
        // Arrange
        $violation = self::createMockViolation();
        $violations = Violations::fromViolations($violation);

        // Act
        $violationsArray = $violations->toArray();

        // Assert
        self::assertSame([$violation], $violationsArray);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function emptyViolationsProvider(): iterable
    {
        yield 'empty' => [
            static fn (): array => [
                'violations' => Violations::fromViolations(),
                'expectedIsEmpty' => true,
            ],
        ];

        yield 'not empty' => [
            static function (): array {
                $violation = self::createMockViolation();

                return [
                    'violations' => Violations::fromViolations($violation),
                    'expectedIsEmpty' => false,
                ];
            },
        ];
    }

    private static function createMockViolation(): ViolationInterface
    {
        return new class implements ViolationInterface {
            public function propertyPath(): string
            {
                throw new \RuntimeException('Not implemented.');
            }

            public function message(): string
            {
                throw new \RuntimeException('Not implemented.');
            }
        };
    }
}
