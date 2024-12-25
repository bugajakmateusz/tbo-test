<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\Buys;

use OpenApi\Annotations as OA;
use Polsl\Application\Command\BuySnack\BuySnack;
use Polsl\Application\Exception\ApplicationException;
use Polsl\Application\Schema\SnackBuySchema;
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
         *     quantity?: int,
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
