<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\Json;

use Symfony\Component\HttpFoundation\Request;
use Tab\Packages\Responder\Response\ResponseFactoryInterface;
use Tab\Packages\Responder\Response\ResponseInterface;

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
