<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\MachineSnacks;

use Polsl\Application\Command\UpdateMachineSnack\UpdateMachineSnack;
use Polsl\Application\Schema\MachineSnackSchema;
use Polsl\Application\Service\ApiProblemJsonResponseFactory;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\Resource;
use Polsl\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Polsl\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Polsl\Packages\MessageBus\Contracts\CommandBusInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Polsl\Packages\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

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
