<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Machines;

use Tab\Application\Command\RemoveMachine\RemoveMachine;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\CommandBusInterface;
use Tab\Packages\Responder\Response\ResponseInterface;

final readonly class RemoveMachineAction
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
    ) {
    }

    public function __invoke(int $machineId): ResponseInterface
    {
        $command = new RemoveMachine($machineId);
        $this->commandBus
            ->handle($command);

        return $this->jsonApiResponseFactory
            ->resourceResponse(statusCode: HttpStatusCodes::HTTP_NO_CONTENT)
        ;
    }
}
