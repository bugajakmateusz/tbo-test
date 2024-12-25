<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Domain\Model\Snack;

use Polsl\Domain\DomainException;
use Polsl\Domain\Model\Snack\Name;
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
    public function test_incorrect_name_throws_exception(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'nameString' => $nameString,
            'expectedExceptionMessage' => $expectedExceptionMessage,
        ] = $createParams();

        // Expect
        $this->expectException(
            DomainException::class,
        );
        $this->expectExceptionMessage(
            $expectedExceptionMessage,
        );

        // Act
        Name::fromString(
            $nameString,
        );
    }

    /**
     * @param \Closure(): array{
     *     nameString: string,
     *     expectedName: string,
     * } $createParams
     *
     * @dataProvider correctNameProvider
     */
    public function test_correct_name_is_accepted(
        \Closure $createParams,
    ): void {
        // Arrange
        [
            'nameString' => $nameString,
            'expectedName' => $expectedName,
        ] = $createParams();

        // Expect
        $Name = Name::fromString(
            $nameString,
        );

        self::assertSame(
            $expectedName,
            $Name->toString(),
        );
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
                'nameString' => Faker::hexBytes(
                    256,
                ),
                'expectedExceptionMessage' => "Name length cannot be higher than '255', '256' passed.",
            ],
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function correctNameProvider(): iterable
    {
        yield 'name' => [
            static function (): array {
                $name = Faker::text();

                return [
                    'nameString' => $name,
                    'expectedName' => $name,
                ];
            },
        ];
        yield 'html name' => [
            static fn (): array => [
                'nameString' => '<p>Test</p>',
                'expectedName' => 'Test',
            ],
        ];
        yield 'whitespaces name' => [
            static fn (): array => [
                'nameString' => " Test\t",
                'expectedName' => 'Test',
            ],
        ];
    }
}
