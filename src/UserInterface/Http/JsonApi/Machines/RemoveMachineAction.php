<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\Machines;

use OpenApi\Annotations as OA;
use Polsl\Application\Command\RemoveMachine\RemoveMachine;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Polsl\Packages\MessageBus\Contracts\CommandBusInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;

final readonly class RemoveMachineAction
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
    ) {}

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
