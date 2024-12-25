<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Prices;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Command\UpdateSnackPrice\UpdateSnackPrice;
use Tab\Application\Exception\ApplicationException;
use Tab\Application\Schema\MachineSchema;
use Tab\Application\Schema\MachineSnackSchema;
use Tab\Application\Schema\SnackPriceSchema;
use Tab\Application\Schema\SnackSchema;
use Tab\Application\Service\ApiProblemJsonResponseFactory;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonApi\Application\Relationships;
use Tab\Packages\JsonApi\Application\Resource;
use Tab\Packages\JsonApi\Application\ResourceIdentifier;
use Tab\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\CommandBusInterface;
use Tab\Packages\Responder\Response\ResponseInterface;
use Tab\Packages\Validator\ValidatorInterface;

final readonly class SetSnackPriceAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {}

    /**
     * Set snack price.
     * This call will set a snack price and save it to price history.
     *
     * @OA\RequestBody(
     *      required=true,
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
     *                      example="snacks-prices",
     *                  ),
     *                  @OA\Property(
     *                      property="attributes",
     *                      type="object",
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
     * @OA\Tag(name="prices")
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

    private function createCommand(Resource $resource): UpdateSnackPrice
    {
        $resource->checkExpectedType(SnackPriceSchema::TYPE);
        /**
         * @var array{
         *     price?: int|float|string,
         * } $attributes
         */
        $attributes = $resource->attributes();
        $relationships = $resource->relationships();
        $machineId = $this->getMachineId($relationships);
        $snackId = $this->getSnackId($relationships);

        return new UpdateSnackPrice(
            (int) $machineId,
            (int) $snackId,
            (float) ($attributes[MachineSnackSchema::ATTRIBUTE_PRICE] ?? 0.0),
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
