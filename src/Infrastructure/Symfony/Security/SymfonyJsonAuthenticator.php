<?php

declare(strict_types=1);

namespace Tab\Infrastructure\Symfony\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;

final class SymfonyJsonAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private const LOGIN_ROUTE = 'api.json.login';
    private const KEY_USERNAME = 'username';
    private const KEY_PASSWORD = 'password';

    public function __construct(
        private readonly JsonSerializerInterface $jsonSerializer,
        private readonly TranslatorInterface $translator,
    ) {}

    public function authenticate(Request $request): Passport
    {
        [
            self::KEY_USERNAME => $identity,
            self::KEY_PASSWORD => $password,
        ] = $this->getCredentials($request);

        $userBadge = new UserBadge($identity);

        return new Passport(
            $userBadge,
            new PasswordCredentials($password),
        );
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->get('_route')
            && Request::METHOD_POST === $request->getMethod()
            && (
                \str_contains((string) $request->getRequestFormat(), 'json')
                || \str_contains((string) $request->getContentTypeFormat(), 'json')
            )
        ;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse(
            ['status' => false, 'message' => 'Auth required'],
            JsonResponse::HTTP_UNAUTHORIZED,
        );
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception,
    ): ?Response {
        $error = 'Invalid credentials.';
        $messageKey = 'login.invalid-credentials';

        $errorMessage = $this->translator
            ->trans(
                $messageKey,
                [],
                'auth',
            )
        ;

        return new JsonResponse(
            [
                'error' => $error,
                'errorMessage' => $errorMessage,
            ],
            JsonResponse::HTTP_UNAUTHORIZED,
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName,
    ): ?Response {
        return null;
    }

    /** @return array{username: string, password: string} */
    private function getCredentials(Request $request): array
    {
        /** @var array{
         *     username?: string,
         *     password?: string,
         * } $credentials
         */
        $credentials = $this->jsonSerializer
            ->decode($request->getContent(), true);

        $usernameKey = self::KEY_USERNAME;
        $passwordKey = self::KEY_PASSWORD;

        return [
            self::KEY_USERNAME => $credentials[$usernameKey]
                ?? throw new BadRequestHttpException("Username key '{$usernameKey}' must be provided."),
            self::KEY_PASSWORD => $credentials[$passwordKey]
                ?? throw new BadRequestHttpException("Password key '{$passwordKey}' must be provided."),
        ];
    }
}
