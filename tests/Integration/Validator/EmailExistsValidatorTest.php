<?php

declare(strict_types=1);

namespace Integration\Validator;

use Tab\Infrastructure\Symfony\Validator\Constraints\EmailExists;
use Tab\Packages\Faker\Faker;
use Tab\Packages\TestCase\Fixtures\Entity\User;
use Tab\Packages\TestCase\IntegrationTestCase;
use Tab\Packages\TestCase\Mother\Entity\UserMother;
use Tab\Packages\TestCase\Validator\TestValidator;

/** @internal */
final class EmailExistsValidatorTest extends IntegrationTestCase
{
    /** @dataProvider emailDataProvider */
    public function test_validate_if_email_exists(\Closure $createParams): void
    {
        // Arrange
        /**
         * @var User   $mentor
         * @var string $searchedEmail
         * @var int    $errorCount
         */
        [
            'user' => $mentor,
            'searchedEmail' => $searchedEmail,
            'errorCount' => $errorCount,
        ] = $createParams();
        /** @var TestValidator $validator */
        $validator = $this->service(TestValidator::class);
        $this->loadEntities($mentor);

        // Act
        $errors = $validator->validate(
            $searchedEmail,
            new EmailExists(),
        );

        // Assert
        self::assertCount($errorCount, $errors->toArray());
    }

    /** @return iterable<string,array{\Closure}> */
    public static function emailDataProvider(): iterable
    {
        yield 'registered email' => [
            static function (): array {
                $email = Faker::email();

                return [
                    'user' => UserMother::withEmail($email),
                    'searchedEmail' => $email,
                    'errorCount' => 1,
                ];
            },
        ];
        yield 'not registered email' => [
            static fn (): array => [
                'user' => UserMother::random(),
                'searchedEmail' => Faker::email(),
                'errorCount' => 0,
            ],
        ];
    }
}
