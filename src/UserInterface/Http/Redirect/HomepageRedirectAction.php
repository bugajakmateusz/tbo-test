<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\Redirect;

use Polsl\Packages\Responder\Response\ResponseFactoryInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;

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
