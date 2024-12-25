<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\MachineSnacks;

use OpenApi\Annotations as OA;
use Polsl\Application\Command\AddNewMachineSnack\AddNewMachineSnack;
use Polsl\Application\Exception\ApplicationException;
use Polsl\Application\Schema\MachineSchema;
use Polsl\Application\Schema\MachineSnackSchema;
use Polsl\Application\Schema\SnackSchema;
use Polsl\Application\Service\ApiProblemJsonResponseFactory;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\Relationships;
use Polsl\Packages\JsonApi\Application\Resource;
use Polsl\Packages\JsonApi\Application\ResourceIdentifier;
use Polsl\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Polsl\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Polsl\Packages\MessageBus\Contracts\CommandBusInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Polsl\Packages\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class CreateNewMachineSnackAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {}

    /**
     * Create new machine snack.
     * This call will create a new machine snack.
     *
     * @OA\RequestBody(
     *      required=true,
     *      description="Attributes needed for creating a new machine snack.",
     *
     * @OA\MediaType(
     *      mediaType="application/json",
     *
     *      @OA\Schema(
     *
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="type",
     *                      type="string",
     *                      example="machine-snacks",
     *                  ),
     *                  @OA\Property(
     *                      property="attributes",
     *                      type="object",
     *                      @OA\Property(
     *                          property="quantity",
     *                          type="int",
     *                          example=5,
     *                      ),
     *                      @OA\Property(
     *                           property="position",
     *                           type="string",
     *                           example="A10",
     *                       ),
     *                      @OA\Property(
     *                            property="price",
     *                            type="float",
     *                            example="3.49",
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                       property="relationships",
     *                       type="object",
     *                       @OA\Property(
     *                          property="snack",
     *                          type="object",
     *                          @OA\Property(
     *                              property="data",
     *                              type="object",
     *                              @OA\Property(
     *                                  property="type",
     *                                  type="string",
     *                                  example="snacks",
     *                              ),
     *                              @OA\Property(
     *                                   property="id",
     *                                   type="integer",
     *                                   example="200",
     *                              ),
     *                          ),
     *                      ),
     *                      @OA\Property(
     *                           property="machine",
     *                           type="object",
     *                           @OA\Property(
     *                               property="data",
     *                               type="object",
     *                               @OA\Property(
     *                                   property="type",
     *                                   type="string",
     *                                   example="machines",
     *                               ),
     *                               @OA\Property(
     *                                    property="id",
     *                                    type="integer",
     *                                    example="200",
     *                               ),
     *                           ),
     *                       ),
     *                  ),
     *              ),
     *          ),
     *      )
     *  )
     *
     * @OA\Tag(name="machine-snacks")
     */
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

    private function createCommand(Resource $resource): AddNewMachineSnack
    {
        $resource->checkExpectedType(MachineSnackSchema::TYPE);
        /**
         * @var array{
         *     position?: string,
         *     quantity?: int|string,
         * } $attributes
         */
        $attributes = $resource->attributes();
        $relationships = $resource->relationships();
        $machineId = $this->getMachineId($relationships);
        $snackId = $this->getSnackId($relationships);

        return new AddNewMachineSnack(
            (int) $machineId,
            (int) $snackId,
            (int) ($attributes[MachineSnackSchema::ATTRIBUTE_QUANTITY] ?? 0),
            $attributes[MachineSnackSchema::ATTRIBUTE_POSITION] ?? '',
        );
    }

    private function getMachineId(Relationships $relationships): string
    {
        $isPresent = $relationships->hasRelationship(MachineSnackSchema::RELATIONSHIP_MACHINE);
        if (false === $isPresent) {
            throw new ApplicationException('Machine id is not present');
        }
        /** @var ResourceIdentifier $machineRelationship */
        $machineRelationship = $relationships->relationship(MachineSnackSchema::RELATIONSHIP_MACHINE);
        $machineRelationship->checkExpectedType(MachineSchema::TYPE);

        return $machineRelationship->id();
    }

    private function getSnackId(Relationships $relationships): string
    {
        $isPresent = $relationships->hasRelationship(MachineSnackSchema::RELATIONSHIP_SNACK);
        if (false === $isPresent) {
            throw new ApplicationException('Snack id is not present');
        }
        /** @var ResourceIdentifier $machineRelationship */
        $machineRelationship = $relationships->relationship(MachineSnackSchema::RELATIONSHIP_SNACK);
        $machineRelationship->checkExpectedType(SnackSchema::TYPE);

        return $machineRelationship->id();
    }
}
