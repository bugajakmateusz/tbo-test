<?php

declare(strict_types=1);

namespace Polsl\Packages\Responder\Response;

use Polsl\Packages\Constants\HttpStatusCodes;

final class Response implements ResponseInterface
{
    private readonly ResponseSpecification $specification;
    private string $targetUrl = '';

    public function __construct(
        private readonly string $content = '',
        private readonly int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $specification = null,
        private readonly string $responseType = self::TYPE_DEFAULT,
        private readonly ?\SplFileObject $file = null,
    ) {
        $isValidType = \in_array(
            $responseType,
            self::VALID_TYPES,
            true,
        );

        if (!$isValidType) {
            throw new \RuntimeException("Response type '{$responseType}' is not valid.");
        }

        if (self::TYPE_FILE === $this->responseType && null === $file) {
            throw new \RuntimeException("Response with type 'file' must pass 'file'.");
        }

        $this->specification = $specification ?? new ResponseSpecification();
    }

    public static function createRedirect(
        string $targetUrl,
        int $statusCode = HttpStatusCodes::HTTP_FOUND,
        ?ResponseSpecification $responseSpecification = null,
    ): self {
        if (!self::isRedirectStatus($statusCode)) {
            throw new \RuntimeException(
                "Unable to create redirect with status code '{$statusCode}'.",
            );
        }

        $response = new self(
            '',
            $statusCode,
            $responseSpecification,
            self::TYPE_REDIRECT,
        );
        $response->targetUrl = $targetUrl;

        return $response;
    }

    public static function createFile(
        \SplFileObject $file,
        int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $responseSpecification = null,
    ): self {
        return new self(
            '',
            $statusCode,
            $responseSpecification,
            self::TYPE_FILE,
            $file,
        );
    }

    public function content(): string
    {
        return $this->content;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function targetUrl(): string
    {
        if (!$this->isRedirect()) {
            throw new \RuntimeException(
                "Unable to get 'targetUrl' from non-redirect response.",
            );
        }

        return $this->targetUrl;
    }

    public function responseType(): string
    {
        return $this->responseType;
    }

    public function isRedirect(): bool
    {
        return self::isRedirectStatus($this->statusCode);
    }

    public function cookies(): array
    {
        return $this->specification
            ->cookies()
        ;
    }

    public function contentType(): ?string
    {
        return $this->specification
            ->contentType()
        ;
    }

    public function sharedMaxAge(): ?int
    {
        return $this->specification
            ->sharedMaxAge()
        ;
    }

    public function maxAge(): ?int
    {
        return $this->specification
            ->maxAge()
        ;
    }

    public function headers(): array
    {
        return $this->specification
            ->headers()
        ;
    }

    public function file(): \SplFileInfo
    {
        if (null === $this->file) {
            throw new \RuntimeException("'file' is not specified.");
        }

        return $this->file;
    }

    private static function isRedirectStatus(int $statusCode): bool
    {
        return $statusCode >= 300 && $statusCode < 400;
    }
}
