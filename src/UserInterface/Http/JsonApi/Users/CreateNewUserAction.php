<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\Users;

use OpenApi\Annotations as OA;
use Polsl\Application\Command\AddNewUser\AddNewUser;
use Polsl\Application\Schema\UserSchema;
use Polsl\Application\Service\ApiProblemJsonResponseFactory;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\Resource;
use Polsl\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Polsl\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Polsl\Packages\MessageBus\Contracts\CommandBusInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Polsl\Packages\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class CreateNewUserAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {}

    /**
     * Register a user.
     * This call will register a user with the provided details.
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
     *                      example="users",
     *                  ),
     *                  @OA\Property(
     *                      property="attributes",
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string",
     *                          example="test@test.pl",
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string",
     *                          example="password123",
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="John",
     *                      ),
     *                      @OA\Property(
     *                          property="surname",
     *                          type="string",
     *                          example="Doe",
     *                      ),
     *                      @OA\Property(
     *                          property="roles",
     *                          type="array",
     *
     *                      @OA\Items(type="string"),
     *                          description="An array of roles.",
     *                          example={"ROLE_USER", "ROLE_ADMIN"},
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      )
     *  )
     *
     * @OA\Response(
     *     response=201,
     *     description="User has been registered successfully.",
     *
     *     @OA\MediaType(
     *         mediaType="application/json",
     *
     *         @OA\Schema(
     *
     *             @OA\Property(
     *                   property="data",
     *                   type="object",
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *
     *     @OA\MediaType(
     *         mediaType="application/json",
     *
     *         @OA\Schema(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="User with this e-mail already exists."
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Tag(name="users")
     */
    public function __invoke(Request $request): ResponseInterface
    {
        $resource = $this->jsonApiSerializer
            ->decodeResource((string) $request->getContent())
        ;
        $registerUserCommand = $this->createCommand($resource);

        $violations = $this->validator
            ->validate($registerUserCommand)
        ;

        if (!$violations->isEmpty()) {
            return $this->apiProblemJsonResponseFactory
                ->unprocessableEntity($violations)
            ;
        }

        $this->commandBus
            ->handle($registerUserCommand)
        ;

        return $this->jsonApiResponseFactory
            ->resourceResponse(statusCode: HttpStatusCodes::HTTP_CREATED)
        ;
    }

    private function createCommand(Resource $resource): AddNewUser
    {
        $resource->checkExpectedType(UserSchema::TYPE);

        /**
         * @var array{
         *     email?: string,
         *     password?: string,
         *     name?: string,
         *     surname?: string,
         *     roles?: array<string>,
         * } $attributes
         */
        $attributes = $resource->attributes();

        return new AddNewUser(
            $attributes[UserSchema::ATTRIBUTE_EMAIL] ?? '',
            $attributes[UserSchema::ATTRIBUTE_PASSWORD] ?? '',
            $attributes[UserSchema::ATTRIBUTE_NAME] ?? '',
            $attributes[UserSchema::ATTRIBUTE_SURNAME] ?? '',
            $attributes[UserSchema::ATTRIBUTE_ROLES] ?? [],
        );
    }
}
