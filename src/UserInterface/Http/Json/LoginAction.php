<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\Json;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Query\LoggedUser\LoggedUser;
use Tab\Packages\MessageBus\Contracts\QueryBusInterface;
use Tab\Packages\Responder\Response\ResponseFactoryInterface;
use Tab\Packages\Responder\Response\ResponseInterface;

final readonly class LoginAction
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private QueryBusInterface $queryBus,
    ) {}

    /**
     * Login a user.
     * This call will log in a user with the provided email and password.
     *
     * @OA\RequestBody(
     *     required=true,
     *
     *     @OA\MediaType(
     *         mediaType="application/json",
     *
     *         @OA\Schema(
     *
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 example="test@test.pl",
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 example="tab-admin",
     *             ),
     *         ),
     *     ),
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="User has been logged.",
     *
     *     @OA\MediaType(
     *         mediaType="application/json",
     *
     *         @OA\Schema(
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true,
     *             ),
     *         ),
     *     ),
     * )
     *
     * @OA\Response(
     *     response=401,
     *     description="Authorization error",
     *
     *     @OA\MediaType(
     *         mediaType="application/json",
     *
     *         @OA\Schema(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid credentials.",
     *             ),
     *             @OA\Property(
     *                 property="errorMessage",
     *                 type="string",
     *                 example="login.invalid-credentials",
     *             ),
     *         ),
     *     ),
     * )
     *
     * @OA\Tag(name="users")
     */
    public function __invoke(Request $request): ResponseInterface
    {
        /** @var \Tab\Domain\Model\Login\LoggedUser $loggedUser */
        $loggedUser = $this->queryBus
            ->handle(new LoggedUser())
        ;

        return $this->responseFactory
            ->jsonResponse(
                [
                    'status' => true,
                ],
            )
        ;
    }
}
