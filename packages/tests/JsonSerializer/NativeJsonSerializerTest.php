<?php

declare(strict_types=1);

namespace Tab\Packages\Tests\JsonSerializer;

use PHPUnit\Framework\TestCase;
use Tab\Packages\Faker\Faker;
use Tab\Packages\JsonSerializer\NativeJsonSerializer;

/** @internal */
final class NativeJsonSerializerTest extends TestCase
{
    public function test_decode_throws_on_error(): void
    {
        $this->expectException(\JsonException::class);
        $this->expectExceptionMessage('Syntax error');

        $serializer = new NativeJsonSerializer();
        $serializer->decode(Faker::words(2));
    }

    /**
     * @dataProvider correctJsonProvider
     *
     * @param \Closure(): array{
     *     jsonString: string,
     *     assoc: bool,
     *     expectedResult: array<string|int, string|int|bool>|object
     * } $createParams
     */
    public function test_correct_json_can_be_decoded(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'jsonString' => $jsonString,
            'assoc' => $assoc,
            'expectedResult' => $expectedResult,
        ] = $createParams();
        $serializer = new NativeJsonSerializer();

        // Act
        $result = $serializer->decode($jsonString, $assoc);

        // Assert
        self::assertEquals($expectedResult, $result);
    }

    public function test_encode_returns_correct_string(): void
    {
        $serializer = new NativeJsonSerializer();
        $result = $serializer->encode(['active' => true]);

        self::assertJsonStringEqualsJsonString('{"active": true}', $result);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function correctJsonProvider(): iterable
    {
        yield 'array' => [
            static fn (): array => [
                'jsonString' => '{"use": true}',
                'assoc' => true,
                'expectedResult' => ['use' => true],
            ],
        ];

        yield 'object' => [
            static function (): array {
                $testCount = Faker::int(0, 999);
                $expectedObject = new \stdClass();
                $expectedObject->testCount = $testCount;

                return [
                    'jsonString' => "{\"testCount\": {$testCount}}",
                    'assoc' => false,
                    'expectedResult' => $expectedObject,
                ];
            },
        ];
    }
}
