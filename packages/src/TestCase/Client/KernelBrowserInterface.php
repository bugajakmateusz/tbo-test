<?php

declare(strict_types=1);

namespace Tab\Packages\TestCase\Client;

use Tab\Infrastructure\Symfony\Security\SymfonyUser;
use Tab\Packages\HttpResponse\CookieInterface;
use Tab\Packages\HttpResponse\ResponseInterface;

interface KernelBrowserInterface
{
    /**
     * @param array<string,array<int|string>|string> $parameters
     * @param array<string,string>                   $headers
     */
    public function request(
        string $method,
        string $uri,
        array $parameters = [],
        string $content = '',
        array $headers = [],
    ): ResponseInterface;

    public function followRedirect(): ResponseInterface;

    public function followRedirects(bool $follow = true): void;

    public function addCookie(CookieInterface $cookie): void;

    public function disableReboot(): void;

    public function loginUser(SymfonyUser $user): void;
}
