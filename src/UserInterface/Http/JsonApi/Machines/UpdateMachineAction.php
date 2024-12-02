<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Machines;

use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Command\UpdateMachine\UpdateMachine;
use Tab\Application\Schema\MachineSchema;
use Tab\Application\Service\ApiProblemJsonResponseFactory;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonApi\Application\Resource;
use Tab\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\CommandBusInterface;
use Tab\Packages\Responder\Response\ResponseInterface;
use Tab\Packages\Validator\ValidatorInterface;

final readonly class UpdateMachineAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {
    }

    public function __invoke(Request $request, int $machineId): ResponseInterface
    {
        $resource = $this->jsonApiSerializer
            ->decodeResource((string) $request->getContent())
        ;
        $updateMachineCommand = $this->createCommand($resource, $machineId);

        $violations = $this->validator
            ->validate(
                $updateMachineCommand,
                $this->resolveValidationGroups($resource),
            );

        if (!$violations->isEmpty()) {
            return $this->apiProblemJsonResponseFactory
                ->unprocessableEntity($violations);
        }

        $this->commandBus
            ->handle($updateMachineCommand);

        return $this->jsonApiResponseFactory
            ->resourceResponse(statusCode: HttpStatusCodes::HTTP_NO_CONTENT)
        ;
    }

    private function createCommand(Resource $resource, int $machineId): UpdateMachine
    {
        $resource->checkExpectedType(MachineSchema::TYPE);
        /**
         * @var array{
         *     location?: null|string,
         *     positionsNumber?: null|int|string,
         *     positionsCapacity?: null|int|string,
         * } $attributes
         */
        $attributes = $resource->attributes();

        return new UpdateMachine(
            $machineId,
            $attributes[MachineSchema::ATTRIBUTE_LOCATION] ?? '',
            (int) ($attributes[MachineSchema::ATTRIBUTE_POSITIONS_NUMBER] ?? 0),
            (int) ($attributes[MachineSchema::ATTRIBUTE_POSITIONS_CAPACITY] ?? 0),
        );
    }

    /** @return string[] */
    private function resolveValidationGroups(Resource $resource): array
    {
        /**
         * @var array{
         *     location?: null|string,
         *     positionsNumber?: null|int|string,
         *     positionsCapacity?: null|int|string,
         * } $attributes
         */
        $attributes = $resource->attributes();
        $validationGroups = ['Default'];
        if (null !== ($attributes[MachineSchema::ATTRIBUTE_LOCATION] ?? null)) {
            $validationGroups[] = 'changedLocation';
        }

        return $validationGroups;
    }
}
