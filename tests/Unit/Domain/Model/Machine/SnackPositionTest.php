<?php

declare(strict_types=1);

namespace Tab\Tests\Unit\Domain\Model\Machine;

use Tab\Domain\DomainException;
use Tab\Domain\Model\Machine\SnackPosition;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\UnitTestCase;

/** @internal */
final class SnackPositionTest extends UnitTestCase
{
    /**
     * @param \Closure(): array{
     *      positionString: string,
     *      expectedExceptionMessage: string,
     *  } $createParams
     *
     * @dataProvider incorrectPositionProvider
     */
    public function test_incorrect_position_throws_exception(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'positionString' => $positionString,
            'expectedExceptionMessage' => $expectedExceptionMessage,
        ] = $createParams();

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        // Act
        SnackPosition::fromString($positionString);
    }

    /**
     * @param \Closure(): array{
     *     positionString: string,
     *     expectedPosition: string,
     * } $createParams
     *
     * @dataProvider correctPositionProvider
     */
    public function test_correct_position_is_accepted(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'positionString' => $positionString,
            'expectedPosition' => $expectedPosition,
        ] = $createParams();

        // Act
        $position = SnackPosition::fromString($positionString);

        // Assert
        self::assertSame($expectedPosition, $position->toString());
    }

    /** @return iterable<string,array{\Closure}> */
    public static function incorrectPositionProvider(): iterable
    {
        yield 'empty' => [
            static fn (): array => [
                'positionString' => '',
                'expectedExceptionMessage' => 'Position cannot be empty.',
            ],
        ];
        yield 'empty html' => [
            static fn (): array => [
                'positionString' => '<p></p>',
                'expectedExceptionMessage' => 'Position cannot be empty.',
            ],
        ];
        yield 'empty space' => [
            static fn (): array => [
                'positionString' => ' ',
                'expectedExceptionMessage' => 'Position cannot be empty.',
            ],
        ];
        yield 'empty tab' => [
            static fn (): array => [
                'positionString' => "\t",
                'expectedExceptionMessage' => 'Position cannot be empty.',
            ],
        ];
        yield 'empty newline' => [
            static fn (): array => [
                'positionString' => "\r\n",
                'expectedExceptionMessage' => 'Position cannot be empty.',
            ],
        ];
        yield 'too long' => [
            static fn (): array => [
                'positionString' => Faker::hexBytes(4),
                'expectedExceptionMessage' => "Position length cannot be higher than '3', '4' passed.",
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function correctPositionProvider(): iterable
    {
        yield 'position' => [
            static function (): array {
                $position = Faker::hexBytes(3);

                return [
                    'positionString' => $position,
                    'expectedPosition' => $position,
                ];
            },
        ];
        yield 'html position' => [
            static fn (): array => [
                'positionString' => '<p>Tes</p>',
                'expectedPosition' => 'Tes',
            ],
        ];
        yield 'whitespaces position' => [
            static fn (): array => [
                'positionString' => " Tes\t",
                'expectedPosition' => 'Tes',
            ],
        ];
    }
}
