<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\Machines;

use OpenApi\Annotations as OA;
use Polsl\Application\Command\AddNewMachine\AddNewMachine;
use Polsl\Application\Schema\MachineSchema;
use Polsl\Application\Service\ApiProblemJsonResponseFactory;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\Resource;
use Polsl\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Polsl\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Polsl\Packages\MessageBus\Contracts\CommandBusInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Polsl\Packages\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class CreateNewMachineAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {}

    /**
     * Create a machine.
     * This call will create a machine.
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
     * @OA\Response(
     *     response=204,
     *     description="Resource was created."
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
