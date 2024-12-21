<?php

declare(strict_types=1);

namespace Tab\Packages\Responder\Response;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;
use Twig\Environment;

final readonly class ResponseFactory implements ResponseFactoryInterface
{
    public function __construct(
        private Environment $twig,
        private JsonSerializerInterface $jsonSerializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function templateResponse(
        string $templateName,
        array $templateParams = [],
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface {
        $content = $this->twig
            ->render(
                $templateName,
                $templateParams,
            )
        ;

        return new Response(
            $content,
            HttpStatusCodes::HTTP_OK,
            $responseSpecification,
            ResponseInterface::TYPE_TEMPLATE,
        );
    }

    public function jsonResponse(
        array $data,
        int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface {
        $content = $this->jsonSerializer
            ->encode($data)
        ;

        return $this->jsonStringResponse(
            $content,
            $statusCode,
            $responseSpecification,
        );
    }

    public function jsonStringResponse(
        string $content,
        int $statusCode = HttpStatusCodes::HTTP_OK,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface {
        return new Response(
            $content,
            $statusCode,
            $responseSpecification,
            ResponseInterface::TYPE_JSON,
        );
    }

    public function routeRedirectResponse(
        string $routeName,
        array $routeParams = [],
        int $statusCode = HttpStatusCodes::HTTP_FOUND,
    ): ResponseInterface {
        $url = $this->urlGenerator
            ->generate($routeName, $routeParams)
        ;

        return $this->redirectResponse($url, $statusCode);
    }

    public function redirectResponse(
        string $url,
        int $statusCode = HttpStatusCodes::HTTP_FOUND,
        ?ResponseSpecification $responseSpecification = null,
    ): ResponseInterface {
        return Response::createRedirect(
            $url,
            $statusCode,
            $responseSpecification,
        );
    }
}
