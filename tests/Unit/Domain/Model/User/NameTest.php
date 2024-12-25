<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Domain\Model\User;

use Polsl\Domain\DomainException;
use Polsl\Domain\Model\User\Name;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class NameTest extends UnitTestCase
{
    /**
     * @param \Closure(): array{
     *      nameString: string,
     *      expectedExceptionMessage: string,
     *  } $createParams
     *
     * @dataProvider incorrectNameProvider
     */
    public function test_incorrect_location_throws_exception(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'nameString' => $nameString,
            'expectedExceptionMessage' => $expectedExceptionMessage,
        ] = $createParams();

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        // Act
        Name::fromString($nameString);
    }

    /**
     * @param \Closure(): array{
     *     nameString: string,
     *     expectedName: string,
     * } $createParams
     *
     * @dataProvider correctNameProvider
     */
    public function test_correct_location_is_accepted(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'nameString' => $nameString,
            'expectedName' => $expectedName,
        ] = $createParams();

        // Act
        $Name = Name::fromString($nameString);

        // Assert
        self::assertSame($expectedName, $Name->toString());
    }

    /** @return iterable<string,array{\Closure}> */
    public static function incorrectNameProvider(): iterable
    {
        yield 'empty' => [
            static fn (): array => [
                'nameString' => '',
                'expectedExceptionMessage' => 'Name cannot be empty.',
            ],
        ];
        yield 'empty html' => [
            static fn (): array => [
                'nameString' => '<p></p>',
                'expectedExceptionMessage' => 'Name cannot be empty.',
            ],
        ];
        yield 'empty space' => [
            static fn (): array => [
                'nameString' => ' ',
                'expectedExceptionMessage' => 'Name cannot be empty.',
            ],
        ];
        yield 'empty tab' => [
            static fn (): array => [
                'nameString' => "\t",
                'expectedExceptionMessage' => 'Name cannot be empty.',
            ],
        ];
        yield 'empty newline' => [
            static fn (): array => [
                'nameString' => "\r\n",
                'expectedExceptionMessage' => 'Name cannot be empty.',
            ],
        ];
        yield 'too long' => [
            static fn (): array => [
                'nameString' => Faker::hexBytes(21),
                'expectedExceptionMessage' => "Name length cannot be higher than '20', '21' passed.",
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function correctNameProvider(): iterable
    {
        yield 'location' => [
            static function (): array {
                $location = Faker::text(20);

                return [
                    'nameString' => $location,
                    'expectedName' => $location,
                ];
            },
        ];
        yield 'html location' => [
            static fn (): array => [
                'nameString' => '<p>Test</p>',
                'expectedName' => 'Test',
            ],
        ];
        yield 'whitespaces location' => [
            static fn (): array => [
                'nameString' => " Test\t",
                'expectedName' => 'Test',
            ],
        ];
    }
}
