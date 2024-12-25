<?php

declare(strict_types=1);

namespace Polsl\Packages\Responder\Response;

use Polsl\Packages\HttpResponse\CookieInterface;

interface ResponseInterface
{
    public const HEADER_CONTENT_TYPE = 'content-type';
    public const TYPE_TEMPLATE = 'template';
    public const TYPE_JSON = 'json';
    public const TYPE_DEFAULT = 'default';
    public const TYPE_REDIRECT = 'redirect';
    public const TYPE_FILE = 'file';
    public const VALID_TYPES = [
        self::TYPE_TEMPLATE,
        self::TYPE_JSON,
        self::TYPE_DEFAULT,
        self::TYPE_REDIRECT,
        self::TYPE_FILE,
    ];

    public function content(): string;

    public function statusCode(): int;

    public function responseType(): string;

    public function isRedirect(): bool;

    public function targetUrl(): string;

    /** @return CookieInterface[] */
    public function cookies(): array;

    public function contentType(): ?string;

    public function sharedMaxAge(): ?int;

    public function maxAge(): ?int;

    /** @return array<string,string[]> */
    public function headers(): array;

    public function file(): \SplFileInfo;
}
