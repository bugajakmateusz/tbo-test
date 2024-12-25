<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Snacks;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Command\UpdateSnack\UpdateSnack;
use Tab\Application\Schema\SnackSchema;
use Tab\Application\Service\ApiProblemJsonResponseFactory;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonApi\Application\Resource;
use Tab\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\CommandBusInterface;
use Tab\Packages\Responder\Response\ResponseInterface;
use Tab\Packages\Validator\ValidatorInterface;

final readonly class UpdateSnackAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {}

    /**
     * Update a snack.
     * This call will update a snack.
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
     *                      example="snacks",
     *                  ),
     *                  @OA\Property(
     *                      property="attributes",
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="Snickers",
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      )
     *  )
     *
     * @OA\Tag(name="snacks")
     */
    public function __invoke(
        Request $request,
        int $snackId,
    ): ResponseInterface {
        $resource = $this->jsonApiSerializer
            ->decodeResource(
                (string) $request->getContent(),
            )
        ;
        $updateSnackCommand = $this->createCommand(
            $resource,
            $snackId,
        );

        $violations = $this->validator
            ->validate(
                $updateSnackCommand,
            )
        ;

        if (!$violations->isEmpty()) {
            return $this->apiProblemJsonResponseFactory
                ->unprocessableEntity(
                    $violations,
                )
            ;
        }

        $this->commandBus
            ->handle(
                $updateSnackCommand,
            )
        ;

        return $this->jsonApiResponseFactory
            ->resourceResponse(
                statusCode: HttpStatusCodes::HTTP_NO_CONTENT,
            )
        ;
    }

    private function createCommand(
        Resource $resource,
        int $snackId,
    ): UpdateSnack {
        $resource->checkExpectedType(
            SnackSchema::TYPE,
        );
        /**
         * @var array{
         *     name?: string,
         * } $attributes
         */
        $attributes = $resource->attributes();

        return new UpdateSnack(
            $snackId,
            $attributes[SnackSchema::ATTRIBUTE_NAME] ?? '',
        );
    }
}
