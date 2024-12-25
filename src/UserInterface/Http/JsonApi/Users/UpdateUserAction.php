<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\Users;

use OpenApi\Annotations as OA;
use Polsl\Application\Command\UpdateUser\UpdateUser;
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

final readonly class UpdateUserAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {}

    /**
     *  Patch a user.
     *  This call will patch a user with the provided details.
     *
     * @OA\RequestBody(
     *       required=true,
     *       description="Attributes needed for user registration.",
     *
     *  @OA\MediaType(
     *       mediaType="application/json",
     *
     *       @OA\Schema(
     *
     *               @OA\Property(
     *                   property="data",
     *                   type="object",
     *                   @OA\Property(
     *                       property="type",
     *                       type="string",
     *                       example="users",
     *                   ),
     *                   @OA\Property(
     *                       property="attributes",
     *                       type="object",
     *                       @OA\Property(
     *                           property="email",
     *                           type="string",
     *                           example="test@test.pl",
     *                       ),
     *                       @OA\Property(
     *                           property="password",
     *                           type="string",
     *                           example="password123",
     *                       ),
     *                       @OA\Property(
     *                           property="name",
     *                           type="string",
     *                           example="John",
     *                       ),
     *                       @OA\Property(
     *                           property="surname",
     *                           type="string",
     *                           example="Doe",
     *                       ),
     *                       @OA\Property(
     *                           property="roles",
     *                           type="array",
     *
     *                       @OA\Items(type="string"),
     *                           description="An array of roles.",
     *                           example={"ROLE_USER", "ROLE_ADMIN"},
     *                       ),
     *                   ),
     *               ),
     *           ),
     *       )
     *   )
     *
     * @OA\Tag(name="users")
     */
    public function __invoke(Request $request, int $userId): ResponseInterface
    {
        $resource = $this->jsonApiSerializer
            ->decodeResource((string) $request->getContent())
        ;
        $updateUserCommand = $this->createCommand($resource, $userId);

        $violations = $this->validator
            ->validate(
                $updateUserCommand,
                $this->resolveValidationGroups($resource),
            );

        if (!$violations->isEmpty()) {
            return $this->apiProblemJsonResponseFactory
                ->unprocessableEntity($violations);
        }

        $this->commandBus
            ->handle($updateUserCommand);

        return $this->jsonApiResponseFactory
            ->resourceResponse(statusCode: HttpStatusCodes::HTTP_NO_CONTENT)
        ;
    }

    private function createCommand(Resource $resource, int $userId): UpdateUser
    {
        $resource->checkExpectedType(UserSchema::TYPE);
        /**
         * @var array{
         *     name?: string|null,
         *     surname?: string|null,
         *     email?: string|null,
         *     password?: string|null,
         *     roles?: string[]|null,
         * } $attributes
         */
        $attributes = $resource->attributes();

        return new UpdateUser(
            $userId,
            $attributes[UserSchema::ATTRIBUTE_NAME] ?? null,
            $attributes[UserSchema::ATTRIBUTE_SURNAME] ?? null,
            $attributes[UserSchema::ATTRIBUTE_EMAIL] ?? null,
            $attributes[UserSchema::ATTRIBUTE_PASSWORD] ?? null,
            $attributes[UserSchema::ATTRIBUTE_ROLES] ?? null,
        );
    }

    /** @return string[] */
    private function resolveValidationGroups(Resource $resource): array
    {
        /**
         * @var array{
         *     email?: string|null
         * } $attributes
         */
        $attributes = $resource->attributes();
        $validationGroups = ['Default'];
        if (null !== ($attributes[UserSchema::ATTRIBUTE_EMAIL] ?? null)) {
            $validationGroups[] = 'changedEmail';
        }

        return $validationGroups;
    }
}
