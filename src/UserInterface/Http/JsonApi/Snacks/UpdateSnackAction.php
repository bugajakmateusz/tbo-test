<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\Snacks;

use OpenApi\Annotations as OA;
use Polsl\Application\Command\UpdateSnack\UpdateSnack;
use Polsl\Application\Schema\SnackSchema;
use Polsl\Application\Service\ApiProblemJsonResponseFactory;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\Resource;
use Polsl\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Polsl\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Polsl\Packages\MessageBus\Contracts\CommandBusInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Polsl\Packages\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

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
