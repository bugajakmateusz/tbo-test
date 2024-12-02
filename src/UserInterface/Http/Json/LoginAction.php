<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\Json;

use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Query\LoggedUser\LoggedUser;
use Tab\Packages\MessageBus\Contracts\QueryBusInterface;
use Tab\Packages\Responder\Response\ResponseFactoryInterface;
use Tab\Packages\Responder\Response\ResponseInterface;

final readonly class LoginAction
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(Request $request): ResponseInterface
    {
        /** @var \Tab\Domain\Model\Login\LoggedUser $loggedUser */
        $loggedUser = $this->queryBus
            ->handle(new LoggedUser())
        ;

        return $this->responseFactory
            ->jsonResponse(
                [
                    'status' => true,
                ],
            )
        ;
    }
}
