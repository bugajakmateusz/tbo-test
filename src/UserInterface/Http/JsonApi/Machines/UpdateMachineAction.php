<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Machines;

use OpenApi\Annotations as OA;
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
    ) {}

    /**
     * Update machine.
     * This call will update a machine with the provided id.
     *
     * @OA\RequestBody(
     *     required=true,
     *     description="Any of the attributes may be included in the request separately.",
     *
     *     @OA\MediaType(
     *         mediaType="application/json",
     *
     *         @OA\Schema(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     example="machines",
     *                 ),
     *                 @OA\Property(
     *                     property="id",
     *                     type="string",
     *                     example="431681",
     *                 ),
     *                 @OA\Property(
     *                     property="attributes",
     *                     type="object",
     *                     @OA\Property(
     *                         property="location",
     *                         type="string",
     *                         example="New York, 7th Street",
     *                     ),
     *                     @OA\Property(
     *                         property="positionsNumber",
     *                         type="number",
     *                     ),
     *                     @OA\Property(
     *                         property="positionsCapacity",
     *                         type="number",
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     )
     * )
     *
     * @OA\Parameter(
     *     name="machineId",
     *     in="path",
     *     required=true,
     *     description="Specify the machine to be updated",
     *
     *     @OA\Schema(
     *         type="int",
     *         example="431681"
     *     )
     * )
     *
     * @OA\Response(
     *     response=204,
     *     description="Resource was modified."
     * )
     * @OA\Response(
     *     response=422,
     *     description="Request payload hasn't met validation requirements.",
     *
     *     @OA\MediaType(
     *         mediaType="application/problem+json",
     *
     *         @OA\Schema(
     *
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 example="https://httpstatuses.com/422"
     *             ),
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 example="Niepoprawne dane żądania"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="number",
     *                 example=422
     *             ),
     *             @OA\Property(
     *                 property="detail",
     *                 type="string",
     *                 example="Przesłano błędne dane w polach: 'location','positionsNumber', 'positionsCapacity'."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="location",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="string",
     *                         example="This value should not be blank."
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="positionsNumber",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="string",
     *                         example="This value should be greater than or equal to 0."
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="positionsCapacity",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="string",
     *                         example="This value should be greater than or equal to 0."
     *                     )
     *                 ),
     *             ),
     *         )
     *     )
     * )
     *
     * @OA\Tag(name="machines")
     */
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
