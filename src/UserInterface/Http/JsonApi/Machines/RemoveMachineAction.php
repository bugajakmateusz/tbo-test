<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Machines;

use OpenApi\Annotations as OA;
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

    /**
     * Remove machine.
     * This call will remove machine with provided id.
     *
     * @OA\Parameter(
     *     name="machineId",
     *     in="path",
     *     required=true,
     *     description="Specify machine to be removed",
     *
     *     @OA\Schema(
     *        type="int",
     *        example="1234"
     *     )
     * ),
     *
     * @OA\Response(
     *     response=204,
     *     description="Resource was deleted",
     * )
     *
     * @OA\Tag(name="machines")
     */
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
