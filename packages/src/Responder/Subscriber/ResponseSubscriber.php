<?php

declare(strict_types=1);

namespace Polsl\Packages\Responder\Subscriber;

use Polsl\Packages\Responder\Response\ResponseInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<
     *     string,
     *     array{
     *         string,
     *         int,
     *     },
     * >
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['response', 110],
        ];
    }

    public function response(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        if (!$controllerResult instanceof ResponseInterface) {
            return;
        }

        $response = match ($controllerResult->responseType()) {
            ResponseInterface::TYPE_JSON => JsonResponse::fromJsonString(
                $controllerResult->content(),
                $controllerResult->statusCode(),
            ),
            ResponseInterface::TYPE_REDIRECT => new RedirectResponse(
                $controllerResult->targetUrl(),
                $controllerResult->statusCode(),
            ),
            ResponseInterface::TYPE_FILE => new BinaryFileResponse(
                $controllerResult->file(),
                $controllerResult->statusCode(),
            ),
            default => new Response(
                $controllerResult->content(),
                $controllerResult->statusCode(),
            ),
        };

        $headers = $response->headers;
        $cookies = $controllerResult->cookies();
        foreach ($cookies as $cookie) {
            $headers->setCookie(
                Cookie::fromString(
                    $cookie->toString(),
                ),
            );
        }

        $contentType = $controllerResult->contentType();
        if (null !== $contentType) {
            $headers->set(
                ResponseInterface::HEADER_CONTENT_TYPE,
                $contentType,
            );
        }

        $sharedMaxAge = $controllerResult->sharedMaxAge();
        if (null !== $sharedMaxAge) {
            $response->setSharedMaxAge($sharedMaxAge);
        }

        $maxAge = $controllerResult->maxAge();
        if (null !== $maxAge) {
            $response->setMaxAge($maxAge);
        }

        foreach ($controllerResult->headers() as $name => $value) {
            $response->headers
                ->set(
                    $name,
                    $value,
                )
            ;
        }

        $event->setResponse($response);
    }
}
