<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\Redirect;

use Tab\Packages\Responder\Response\ResponseFactoryInterface;
use Tab\Packages\Responder\Response\ResponseInterface;

final readonly class HomepageRedirectAction
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function __invoke(): ResponseInterface
    {
        return $this->responseFactory
            ->redirectResponse(
                '/app/',
            )
        ;
    }
}
