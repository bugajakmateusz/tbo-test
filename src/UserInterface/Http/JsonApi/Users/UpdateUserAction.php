<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Users;

use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Command\UpdateUser\UpdateUser;
use Tab\Application\Schema\UserSchema;
use Tab\Application\Service\ApiProblemJsonResponseFactory;
use Tab\Packages\Constants\HttpStatusCodes;
use Tab\Packages\JsonApi\Application\Resource;
use Tab\Packages\JsonApi\Contracts\JsonApiSerializerInterface;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\CommandBusInterface;
use Tab\Packages\Responder\Response\ResponseInterface;
use Tab\Packages\Validator\ValidatorInterface;

final readonly class UpdateUserAction
{
    public function __construct(
        private JsonApiSerializerInterface $jsonApiSerializer,
        private CommandBusInterface $commandBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private ValidatorInterface $validator,
        private ApiProblemJsonResponseFactory $apiProblemJsonResponseFactory,
    ) {}

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
