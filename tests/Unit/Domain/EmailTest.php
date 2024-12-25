<?php

declare(strict_types=1);

namespace Polsl\Tests\Unit\Domain;

use Polsl\Domain\DomainException;
use Polsl\Domain\Email;
use Polsl\Packages\Faker\Faker;
use Polsl\Packages\TestCase\UnitTestCase;

/** @internal */
final class EmailTest extends UnitTestCase
{
    /**
     * @dataProvider invalidEmailProvider
     *
     * @param \Closure(): string $emailGenerator
     */
    public function test_invalid_emails_are_not_valid(\Closure $emailGenerator): void
    {
        // Arrange
        $email = $emailGenerator();

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("E-mail '{$email}' is not valid");

        // Act
        Email::fromString($email);
    }

    public function test_valid_emails_are_accepted(): void
    {
        // Arrange
        $emailString = Faker::email();

        // Act
        $email = Email::fromString($emailString);

        // Assert
        self::assertSame($emailString, $email->toString());
    }

    public function test_emails_are_normalized(): void
    {
        // Arrange
        $emailString = "    MaIl@example.com       \r\n";
        $normalizedEmail = \mb_convert_case(
            \trim($emailString),
            \MB_CASE_LOWER,
        );

        // Act
        $email = Email::fromString($emailString);

        // Assert
        self::assertSame($normalizedEmail, $email->toString());
    }

    public function test_empty_email_is_not_allowed(): void
    {
        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('E-mail cannot be empty.');

        // Act
        Email::fromString('');
    }

    public function test_email_length_is_limited(): void
    {
        // Arrange
        $emailLength = Email::MAX_LENGTH + 1;
        $email = Faker::hexBytes($emailLength);

        // Expect
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("E-mail length cannot be higher than '250', '{$emailLength}' passed.");

        // Act
        Email::fromString($email);
    }

    public function test_email_can_be_created_with_maximum_length(): void
    {
        // Arrange
        $mailPart = 'mail@mail.com';
        $hexPart = Faker::hexBytes(Email::MAX_LENGTH - \strlen($mailPart));
        $emailString = "{$hexPart}{$mailPart}";

        // Act
        $email = Email::fromString($emailString);

        // Assert
        self::assertSame($emailString, $email->toString());
    }

    /** @dataProvider equalsToProvider */
    public function test_equals_to(
        \Closure $createParams,
    ): void {
        // Arrange
        /**
         * @var string $firstEmail
         * @var string $secondEmail
         * @var bool   $expectedResult
         */
        [
            'firstEmail' => $firstEmail,
            'secondEmail' => $secondEmail,
            'equal' => $expectedResult,
        ] = $createParams();

        $first = Email::fromString($firstEmail);
        $second = Email::fromString($secondEmail);

        // Act
        $isEquals = $first->equalsTo($second);

        // Assert
        self::assertSame($expectedResult, $isEquals);
    }

    /** @return iterable<string,array{\Closure}> */
    public static function invalidEmailProvider(): iterable
    {
        yield 'space' => [
            static fn (): string => 'mail space@gmail.com',
        ];
        yield 'tab' => [
            static fn (): string => "mail\ttab@mail.com",
        ];
        yield 'new line' => [
            static fn (): string => "mail\nnewline@example.com",
        ];
        yield 'with double [at] symbol' => [
            static fn (): string => 'double@@example.com',
        ];
        yield 'with two [at] symbol' => [
            static fn (): string => 'two@at@example.com',
        ];
        yield 'with nul-byte symbol' => [
            static fn (): string => "mail\ttwo\0@example.com",
        ];
    }

    /** @return iterable<string,array{\Closure}> */
    public static function equalsToProvider(): iterable
    {
        yield 'equals' => [
            static function (): array {
                $mail = Faker::safeEmail();

                return [
                    'firstEmail' => $mail,
                    'secondEmail' => $mail,
                    'equal' => true,
                ];
            },
        ];
        yield 'not equals' => [
            static fn () => [
                'firstEmail' => Faker::safeEmail(),
                'secondEmail' => Faker::safeEmail(),
                'equal' => false,
            ],
        ];
        yield 'with nul-byte' => [
            static fn () => [
                'firstEmail' => "two\0@example.com",
                'secondEmail' => 'two@example.com',
                'equal' => true,
            ],
        ];
    }
}
