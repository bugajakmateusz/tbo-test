<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\Machine;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Machine\Location;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class LocationTest extends UnitTestCase
{
    /**
     * @param \Closure(): array{
     *      locationString: string,
     *      expectedExceptionMessage: string,
     *  } $createParams
     *
     * @dataProvider incorrectLocationProvider
     */
    public function test_incorrect_location_throws_exception(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'locationString' => $locationString,
            'expectedExceptionMessage' => $expectedExceptionMessage,
        ] = $createParams();

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        // Act
        Location::fromString($locationString);
    }

    /**
     * @param \Closure(): array{
     *     locationString: string,
     *     expectedLocation: string,
     * } $createParams
     *
     * @dataProvider correctLocationProvider
     */
    public function test_correct_location_is_accepted(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'locationString' => $locationString,
            'expectedLocation' => $expectedLocation,
        ] = $createParams();

        // Expect
        $Location = Location::fromString($locationString);

        self::assertSame($expectedLocation, $Location->toString());
    }

    /** @return iterable<string,array{\Closure}> */
    public static function incorrectLocationProvider(): iterable
    {
        yield 'empty' => [
            static fn (): array => [
                'locationString' => '',
                'expectedExceptionMessage' => 'Location cannot be empty.',
            ],
        ];
        yield 'empty html' => [
            static fn (): array => [
                'locationString' => '<p></p>',
                'expectedExceptionMessage' => 'Location cannot be empty.',
            ],
        ];
        yield 'empty space' => [
            static fn (): array => [
                'locationString' => ' ',
                'expectedExceptionMessage' => 'Location cannot be empty.',
            ],
        ];
        yield 'empty tab' => [
            static fn (): array => [
                'locationString' => "\t",
                'expectedExceptionMessage' => 'Location cannot be empty.',
            ],
        ];
        yield 'empty newline' => [
            static fn (): array => [
                'locationString' => "\r\n",
                'expectedExceptionMessage' => 'Location cannot be empty.',
            ],
        ];
        yield 'too long' => [
            static fn (): array => [
                'locationString' => Faker::hexBytes(256),
                'expectedExceptionMessage' => "Location length cannot be higher than '255', '256' passed.",
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function correctLocationProvider(): iterable
    {
        yield 'location' => [
            static function (): array {
                $location = Faker::text(100);

                return [
                    'locationString' => $location,
                    'expectedLocation' => $location,
                ];
            },
        ];
        yield 'html location' => [
            static fn (): array => [
                'locationString' => '<p>Test</p>',
                'expectedLocation' => 'Test',
            ],
        ];
        yield 'whitespaces location' => [
            static fn (): array => [
                'locationString' => " Test\t",
                'expectedLocation' => 'Test',
            ],
        ];
    }
}
