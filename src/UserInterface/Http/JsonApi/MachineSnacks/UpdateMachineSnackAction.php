<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\MachineSnacks;

use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Command\UpdateMachineSnack\UpdateMachineSnack;
use Tab\Application\Schema\MachineSnackSchema;
use Tab\Application\Service\ApiProblemJsonResponseFactory;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonApi\Application\Resource;
use Tab\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\CommandBusInterface;
use Tab\Packages\Responder\Response\ResponseInterface;
use Tab\Packages\Validator\ValidatorInterface;

final readonly class UpdateMachineSnackAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {}

    public function __invoke(Request $request): ResponseInterface
    {
        $resource = $this->jsonApiSerializer
            ->decodeResource((string) $request->getContent())
        ;
        $updateMachineSnackCommand = $this->createCommand($resource);

        $violations = $this->validator
            ->validate($updateMachineSnackCommand);

        if (!$violations->isEmpty()) {
            return $this->apiProblemJsonResponseFactory
                ->unprocessableEntity($violations);
        }

        $this->commandBus
            ->handle($updateMachineSnackCommand);

        return $this->jsonApiResponseFactory
            ->resourceResponse(statusCode: HttpStatusCodes::HTTP_NO_CONTENT);
    }

    private function createCommand(Resource $resource): UpdateMachineSnack
    {
        $resource->checkExpectedType(MachineSnackSchema::TYPE);
        /**
         * @var array{
         *     quantity?: int|string,
         * } $attributes
         */
        $attributes = $resource->attributes();

        return new UpdateMachineSnack(
            (int) $resource->id(),
            (int) ($attributes[MachineSnackSchema::ATTRIBUTE_QUANTITY] ?? 0),
        );
    }
}
