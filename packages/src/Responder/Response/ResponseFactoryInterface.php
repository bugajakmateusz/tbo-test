<?php

declare(strict_types=1);

namespace Polsl\Packages\Responder\Response;

use Polsl\Packages\Constants\HttpStatusCodes;

interface ResponseFactoryInterface
{
    /** @param array<string,mixed> $templateParams */
    public function templateResponse(
        string $templateName,
        array $templateParams = [],
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface;

    /** @param array<int|string,mixed> $data */
    public function jsonResponse(
        array $data,
        int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface;

    public function jsonStringResponse(
        string $content,
        int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface;

    /** @param array<string,mixed> $routeParams */
    public function routeRedirectResponse(
        string $routeName,
        array $routeParams = [],
        int $statusCode = HttpStatusCodes::HTTP_FOUND,
    ): ResponseInterface;

    public function redirectResponse(
        string $url,
        int $statusCode = HttpStatusCodes::HTTP_FOUND,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface;
}
