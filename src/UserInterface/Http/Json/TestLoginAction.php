<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\Json;

use Polsl\Packages\Responder\Response\ResponseFactoryInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class TestLoginAction
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function __invoke(Request $request): ResponseInterface
    {
        return $this->responseFactory
            ->templateResponse(
                'pages/login.html.twig',
                [
                    'last_username' => '$lastUsername',
                    'error' => null,
                ],
            )
        ;
    }
}
