<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Machines;

use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Command\AddNewMachine\AddNewMachine;
use Tab\Application\Schema\MachineSchema;
use Tab\Application\Service\ApiProblemJsonResponseFactory;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonApi\Application\Resource;
use Tab\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\CommandBusInterface;
use Tab\Packages\Responder\Response\ResponseInterface;
use Tab\Packages\Validator\ValidatorInterface;

final readonly class CreateNewMachineAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {
    }

    public function __invoke(Request $request): ResponseInterface
    {
        $resource = $this->jsonApiSerializer
            ->decodeResource((string) $request->getContent())
        ;
        $createNewMachineCommand = $this->createCommand($resource);

        $violations = $this->validator
            ->validate($createNewMachineCommand);

        if (!$violations->isEmpty()) {
            return $this->apiProblemJsonResponseFactory
                ->unprocessableEntity($violations);
        }

        $this->commandBus
            ->handle($createNewMachineCommand);

        return $this->jsonApiResponseFactory
            ->resourceResponse(statusCode: HttpStatusCodes::HTTP_CREATED);
    }

    private function createCommand(Resource $resource): AddNewMachine
    {
        $resource->checkExpectedType(MachineSchema::TYPE);
        /**
         * @var array{
         *     location?: string,
         *     positionsNumber?: int|string,
         *     positionsCapacity?: int|string,
         * } $attributes
         */
        $attributes = $resource->attributes();

        return new AddNewMachine(
            $attributes[MachineSchema::ATTRIBUTE_LOCATION] ?? '',
            (int) ($attributes[MachineSchema::ATTRIBUTE_POSITIONS_NUMBER] ?? 0),
            (int) ($attributes[MachineSchema::ATTRIBUTE_POSITIONS_CAPACITY] ?? 0),
        );
    }
}
