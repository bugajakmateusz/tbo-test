<?php

declare(strict_types=1);

namespace Tab\Packages\HttpResponse;

interface ResponseInterface
{
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_FOUND = 302;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    public const HEADER_CONTENT_TYPE = 'content-type';
    public const HEADER_LOCATION = 'location';

    public function statusCode(): int;

    public function isOk(): bool;

    public function uri(): string;

    public function content(): string;

    public function contentType(): string;

    /** @return array<int|string, null|array<int, null|string>|string> */
    public function headers(): array;

    public function location(): string;

    public function isRedirection(): bool;

    /** @return CookieInterface[] */
    public function cookies(): array;

    public function filePath(): string;
}
