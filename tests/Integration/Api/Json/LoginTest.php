<?php

declare(strict_types=1);

namespace Polsl\Tests\Integration\Api\Json;

use Polsl\Packages\Faker\Faker;
use Polsl\Packages\HttpResponse\ResponseInterface;
use Polsl\Packages\TestCase\Fixtures\Entity\User;
use Polsl\Packages\TestCase\IntegrationTestCase;
use Polsl\Packages\TestCase\Mother\Entity\UserMother;
use Polsl\Tests\TestCase\Application\Client\LoginClient;

/** @internal */
final class LoginTest extends IntegrationTestCase
{
    /**
     * @dataProvider loginInvalidDataProvider
     *
     * @param \Closure(): array{
     *     user: User,
     *     password: string,
     *     expectedError: string,
     * } $generateParams
     */
    public function test_unable_to_login_to_some_accounts(\Closure $generateParams): void
    {
        [
            'user' => $user,
            'password' => $password,
            'expectedError' => $expectedError
        ] = $generateParams();
        $this->loadEntities($user);

        $jsonSerializer = $this->jsonSerializer();
        $loginClient = $this->classService(LoginClient::class);
        $response = $loginClient->login($user->email, $password);

        self::assertSame(ResponseInterface::HTTP_UNAUTHORIZED, $response->statusCode());
        self::assertJson($response->content());

        /**
         * @var array{
         *     error?: string,
         *     errorMessage?: string,
         * } $responseData
         */
        $responseData = $jsonSerializer->decode($response->content(), true);

        self::assertArrayHasKey('error', $responseData);
        self::assertArrayHasKey('errorMessage', $responseData);
        self::assertSame($expectedError, $responseData['error']);
        self::assertNotEmpty($responseData['errorMessage']);
    }

    public function test_user_can_login_by_username_and_password(): void
    {
        // Arrange
        $user = UserMother::random();
        $this->loadEntities($user);
        $jsonSerializer = $this->jsonSerializer();
        $loginClient = $this->classService(LoginClient::class);

        // Act
        $response = $loginClient->login($user->email, $user->password);

        // Assert
        self::assertSame(ResponseInterface::HTTP_OK, $response->statusCode());
        self::assertJson($response->content());

        /**
         * @var array{
         *     status?: bool,
         * } $responseData
         */
        $responseData = $jsonSerializer->decode($response->content(), true);

        self::assertArrayHasKey('status', $responseData);
        self::assertTrue($responseData['status']);
    }

    /** @return iterable<string, array{\Closure}> */
    public static function loginInvalidDataProvider(): iterable
    {
        yield 'invalid password' => [
            static function (): array {
                $user = UserMother::random();

                return [
                    'user' => $user,
                    'password' => Faker::password(),
                    'expectedError' => 'Invalid credentials.',
                ];
            },
        ];
    }
}
