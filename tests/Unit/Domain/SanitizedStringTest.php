<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Domain;

use Polsl\Domain\DomainException;
use Polsl\Domain\SanitizedString;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class SanitizedStringTest extends UnitTestCase
{
    /**
     * @param \Closure(): array{
     *     input: string,
     *     expectedOutput: string,
     * } $createParams
     *
     * @dataProvider sanitizedStringProvider
     */
    public function test_sanitized_string_sanitizes_string(\Closure $createParams): void
    {
        // Arrange
        [
            'input' => $input,
            'expectedOutput' => $expectedOutput
        ] = $createParams();
        $sanitizedString = SanitizedString::create($input);

        // Act
        $output = $sanitizedString->toString();

        // Assert
        self::assertSame($expectedOutput, $output);
    }

    public function test_check_is_empty_throws_on_empty_value(): void
    {
        // Arrange
        $sanitizedString = SanitizedString::create('');
        $entityName = Faker::words(1);

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("{$entityName} cannot be empty.");

        // Act
        $sanitizedString->checkIsEmpty($entityName);
    }

    public function test_check_is_empty_do_nothing_on_non_empty_value(): void
    {
        // Arrange
        $sanitizedString = SanitizedString::create(Faker::hexBytes(1));
        $entityName = Faker::words(1);

        // Expect
        $this->expectNotToPerformAssertions();

        // Act
        $sanitizedString->checkIsEmpty($entityName);
    }

    /** @dataProvider tooLongValueProvider */
    public function test_check_length_throws_on_too_long_value(\Closure $paramsGenerator): void
    {
        // Arrange
        /**
         * @var int    $maxLength
         * @var int    $textLength
         * @var string $text
         */
        [
            'maxLength' => $maxLength,
            'textLength' => $textLength,
            'text' => $text,
        ] = $paramsGenerator();
        $sanitizedString = SanitizedString::create($text);
        $entityName = Faker::words(1);

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            "{$entityName} length cannot be higher than '{$maxLength}', '{$textLength}' passed.",
        );

        // Act
        $sanitizedString->checkMaxLength($maxLength, $entityName);
    }

    public function test_check_max_length_with_zero_max_length(): void
    {
        // Arrange
        $sanitizedString = SanitizedString::create(Faker::word());

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Maximum length cannot be lower than 1.');

        // Act
        $sanitizedString->checkMaxLength(0, Faker::word());
    }

    public function test_check_length_do_nothing_with_good_value(): void
    {
        // Arrange
        $maxLength = Faker::int(1, 99);
        $text = Faker::hexBytes($maxLength);
        $sanitizedString = SanitizedString::create($text);

        // Expect
        $this->expectNotToPerformAssertions();

        // Act
        $sanitizedString->checkMaxLength($maxLength, 'Text');
    }

    public function test_check_regex(): void
    {
        // Arrange
        $sanitizedString = SanitizedString::create(
            Faker::words(5),
        );
        $regex = '/^[0-9a-f]+$/i';

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Text has not meet regex pattern.');

        // Act
        $sanitizedString->checkMatchesPattern($regex, 'Text');
    }

    public function test_check_regex_do_nothing_with_good_value(): void
    {
        // Arrange
        $sanitizedString = SanitizedString::create(
            Faker::hexBytes(),
        );
        $regex = '/^[0-9a-f]+$/i';

        // Expect
        $this->expectNotToPerformAssertions();

        // Act
        $sanitizedString->checkMatchesPattern($regex, 'Text');
    }

    /**
     * @param \Closure(): array{
     *     inputString: string,
     *     encodeQuotes: bool,
     *     expectedOutputString: string,
     * } $createParams
     *
     * @dataProvider quotesEncodingTest
     */
    public function test_quotes_encoding(\Closure $createParams): void
    {
        // Arrange
        [
            'inputString' => $inputString,
            'encodeQuotes' => $encodeQuotes,
            'expectedOutputString' => $expectedOutputString
        ] = $createParams();

        // Act
        $sanitizedString = SanitizedString::create($inputString, $encodeQuotes);

        // Assert
        self::assertSame($expectedOutputString, $sanitizedString->toString());
    }

    /** @dataProvider exactLengthInvalidDataProvider */
    public function test_exact_length_throws_exception(\Closure $createParams): void
    {
        // Arrange
        /**
         * @var string $inputString
         * @var int    $length
         * @var string $exceptionMsg
         * @var string $entityName
         */
        [
            'inputString' => $inputString,
            'length' => $length,
            'exceptionMsg' => $exceptionMsg,
            'entityName' => $entityName,
        ] = $createParams();
        $sanitizedString = SanitizedString::create($inputString);

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($exceptionMsg);

        // Act
        $sanitizedString->checkExactLength($length, $entityName);
    }

    public function test_exact_length_do_not_throw_exception(): void
    {
        // Arrange
        $length = Faker::int(1, 100);
        $inputString = Faker::hexBytes($length);
        $sanitizedString = SanitizedString::create($inputString);

        // Expect
        $this->expectNotToPerformAssertions();

        // Act
        $sanitizedString->checkExactLength($length, Faker::word());
    }

    /** @return iterable<string, array{\Closure}> */
    public static function sanitizedStringProvider(): iterable
    {
        yield 'plain' => [
            static function (): array {
                $words = Faker::words(3);

                return [
                    'input' => $words,
                    'expectedOutput' => $words,
                ];
            },
        ];

        yield 'trim' => [
            static fn (): array => [
                'input' => "\0\t some name \r\n",
                'expectedOutput' => 'some name',
            ],
        ];

        yield 'sanitize html' => [
            static function (): array {
                $words = Faker::words(3);

                return [
                    'input' => "<li>{$words}",
                    'expectedOutput' => $words,
                ];
            },
        ];

        yield 'sanitize script' => [
            static function (): array {
                $words = Faker::words(3);

                return [
                    'input' => "<script>alert('{$words}')</script>",
                    'expectedOutput' => "alert(&#39;{$words}&#39;)",
                ];
            },
        ];

        yield 'polish diacritics' => [
            static fn (): array => [
                'input' => 'Zażółć gęslą jaźń',
                'expectedOutput' => 'Zażółć gęslą jaźń',
            ],
        ];
    }

    /** @return iterable<string, array{\Closure}> */
    public static function quotesEncodingTest(): iterable
    {
        yield 'do not encode quotes with single quotes' => [
            static fn (): array => [
                'inputString' => "'quotes'",
                'encodeQuotes' => false,
                'expectedOutputString' => "'quotes'",
            ],
        ];

        yield 'do not encode quotes with double quotes' => [
            static fn (): array => [
                'inputString' => '"quotes"',
                'encodeQuotes' => false,
                'expectedOutputString' => '"quotes"',
            ],
        ];

        yield 'encode quotes with single quotes' => [
            static fn (): array => [
                'inputString' => "'quotes'",
                'encodeQuotes' => true,
                'expectedOutputString' => '&#39;quotes&#39;',
            ],
        ];

        yield 'encode quotes with double quotes' => [
            static fn (): array => [
                'inputString' => '"quotes"',
                'encodeQuotes' => true,
                'expectedOutputString' => '&#34;quotes&#34;',
            ],
        ];
    }

    /** @return iterable<string, array{\Closure}> */
    public static function tooLongValueProvider(): iterable
    {
        yield 'ascii text' => [
            static function (): array {
                $maxLength = Faker::int(1, 99);
                $textLength = $maxLength + 1;

                return [
                    'maxLength' => $maxLength,
                    'textLength' => $textLength,
                    'text' => Faker::hexBytes($textLength),
                ];
            },
        ];

        yield 'text with emojis' => [
            static fn (): array => [
                'maxLength' => 6,
                'textLength' => 7,
                'text' => 'Hello ☺️',
            ],
        ];
    }

    /** @return iterable<string, array{\Closure}> */
    public static function exactLengthInvalidDataProvider(): iterable
    {
        yield 'too low' => [
            static function (): array {
                $actualLength = Faker::int(2, 100);
                $testLength = $actualLength - 1;
                $inputString = Faker::hexBytes($actualLength);
                $entityName = Faker::word();

                return [
                    'inputString' => $inputString,
                    'length' => $testLength,
                    'exceptionMsg' => "{$entityName} length should be equal to '{$testLength}', '{$actualLength}' passed.",
                    'entityName' => $entityName,
                ];
            },
        ];
        yield 'too high' => [
            static function (): array {
                $actualLength = Faker::int(2, 100);
                $testLength = $actualLength + 1;
                $inputString = Faker::hexBytes($actualLength);
                $entityName = Faker::word();

                return [
                    'inputString' => $inputString,
                    'length' => $testLength,
                    'exceptionMsg' => "{$entityName} length should be equal to '{$testLength}', '{$actualLength}' passed.",
                    'entityName' => $entityName,
                ];
            },
        ];
        yield 'lower than 1' => [
            static fn (): array => [
                'inputString' => Faker::word(),
                'length' => 0,
                'exceptionMsg' => 'Exact length cannot be lower than 1.',
                'entityName' => Faker::word(),
            ],
        ];
    }
}
