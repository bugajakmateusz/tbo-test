<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Buys;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Command\BuySnack\BuySnack;
use Tab\Application\Exception\ApplicationException;
use Tab\Application\Schema\SnackBuySchema;
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

final readonly class BuySnackAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {}

    /**
     * Buy a snack.
     *
     * @OA\RequestBody(
     *      required=true,
     *      description="Attributes needed for user registration.",
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
     *                      example="buys",
     *                  ),
     *                  @OA\Property(
     *                      property="attributes",
     *                      type="object",
     *                      @OA\Property(
     *                          property="snackId",
     *                          type="integer",
     *                          example=1,
     *                      ),
     *                      @OA\Property(
     *                          property="price",
     *                          type="float",
     *                          example=12.22,
     *                      ),
     *                  @OA\Property(
     *                           property="quantity",
     *                           type="int",
     *                           example=5,
     *                       ),
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
     *                                   example="1",
     *                              ),
     *                          ),
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      )
     *  )
     *
     * @OA\Tag(name="buys")
     */
    public function __invoke(Request $request): ResponseInterface
    {
        $resource = $this->jsonApiSerializer
            ->decodeResource((string) $request->getContent())
        ;
        $buySnackCommand = $this->createCommand($resource);

        $violations = $this->validator
            ->validate($buySnackCommand);

        if (!$violations->isEmpty()) {
            return $this->apiProblemJsonResponseFactory
                ->unprocessableEntity($violations);
        }

        $this->commandBus
            ->handle($buySnackCommand);

        return $this->jsonApiResponseFactory
            ->resourceResponse(statusCode: HttpStatusCodes::HTTP_CREATED);
    }

    /** @throws ApplicationException */
    private function createCommand(Resource $resource): BuySnack
    {
        $resource->checkExpectedType(SnackBuySchema::TYPE);
        /**
         * @var array{
         *     price?: float,
         * } $attributes
         */
        $attributes = $resource->attributes();
        $relationships = $resource->relationships();
        $snackId = $this->getSnackId($relationships);

        return new BuySnack(
            (int) $snackId,
            (float) ($attributes[SnackBuySchema::ATTRIBUTE_PRICE] ?? 0.0),
            (int) ($attributes[SnackBuySchema::ATTRIBUTE_QUANTITY] ?? 0),
        );
    }

    private function getSnackId(Relationships $relationships): string
    {
        $isPresent = $relationships->hasRelationship(SnackBuySchema::RELATIONSHIP_SNACK);
        if (false === $isPresent) {
            throw new ApplicationException('Snack id is not present');
        }
        /** @var ResourceIdentifier $snackRelationship */
        $snackRelationship = $relationships->relationship(SnackBuySchema::RELATIONSHIP_SNACK);
        $snackRelationship->checkExpectedType(SnackSchema::TYPE);

        return $snackRelationship->id();
    }
}
