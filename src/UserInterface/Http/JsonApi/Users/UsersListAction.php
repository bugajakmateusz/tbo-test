<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Users;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Query\UsersList\UsersList;
use Tab\Application\Query\UsersList\UsersListView;
use Tab\Packages\Constants\JsonApi;
use Tab\Packages\JsonApi\Application\Includes;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\QueryBusInterface;
use Tab\Packages\ResourcesList\QueryParamsExtractorFactory;
use Tab\Packages\Responder\Response\ResponseInterface;

final readonly class UsersListAction
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private QueryParamsExtractorFactory $queryParamsExtractorFactory,
    ) {
    }

    /**
     * Get information about users.
     *
     * This endpoint returns information about users. You can filter the results to get information
     * about the currently logged-in user by using the "?filter{me}=1" query parameter.
     *
     * @OA\Parameter(
     *      name="filter[me]",
     *      in="query",
     *      required=false,
     *      description="Filter to mark response should include only currently logged user data.",
     *
     *      @OA\Schema(
     *         type="int",
     *         example="1"
     *      )
     *  ),
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns information about users according to passed filters.",
     *
     *     @OA\JsonContent(
     *         oneOf={
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(
     *                         property="totalItems",
     *                         type="number"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(
     *                             property="type",
     *                             type="string"
     *                         ),
     *                         @OA\Property(
     *                             property="id",
     *                             type="string"
     *                         ),
     *                         @OA\Property(
     *                             property="attributes",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="name",
     *                                 type="string"
     *                             ),
     *                             @OA\Property(
     *                                 property="surname",
     *                                 type="string"
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(
     *                         property="totalItems",
     *                         type="number"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(
     *                             property="type",
     *                             type="string"
     *                         ),
     *                         @OA\Property(
     *                             property="id",
     *                             type="string"
     *                         ),
     *                         @OA\Property(
     *                             property="attributes",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="name",
     *                                 type="string"
     *                             ),
     *                             @OA\Property(
     *                                 property="surname",
     *                                 type="string"
     *                             ),
     *                             @OA\Property(
     *                                 property="email",
     *                                 type="string",
     *                                 description="User's email (only returned when filter{me}=1)"
     *                             ),
     *                             @OA\Property(
     *                                 property="roles",
     *                                 type="array",
     *
     *                                 @OA\Items(
     *                                     type="string"
     *                                 ),
     *                                 description="User's roles (only returned when filter{me}=1)"
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         },
     *
     *         @OA\Examples(
     *             example="result",
     *             value={
     *                 "meta":{"totalItems":2},
     *                 "data":{
     *                     {"type":"users","id":"1","attributes":{"name":"test","surname":"test1"}},
     *                     {"type":"users","id":"2","attributes":{"name":"test","surname":"test1"}}
     *                 }
     *             },
     *             summary="List"
     *         ),
     *         @OA\Examples(
     *             example="filter me",
     *             value={
     *                 "meta":{"totalItems":1},
     *                 "data":{
     *                     {"type":"users","id":"1","attributes":{"email":"test@test.pl","name":"test","surname":"test1","roles":{"ROLE_USER"}}}
     *                 }
     *             },
     *             summary="Filter me"
     *         )
     *     )
     * ),
     *
     * @OA\Tag(name="users")
     */
    public function __invoke(Request $request): ResponseInterface
    {
        $queryParamsExtractor = $this->queryParamsExtractorFactory
            ->fromRequestQueryParams($request)
        ;

        $includes = $queryParamsExtractor->includes();
        $filters = $queryParamsExtractor->filters();

        $usersListQuery = new UsersList(
            $filters,
            $queryParamsExtractor->page(),
        );
        /** @var UsersListView $usersListView */
        $usersListView = $this->queryBus
            ->handle($usersListQuery)
        ;

        $totalItems = $usersListView->totalItems;
        $fields = $usersListView->fields;

        return $this->jsonApiResponseFactory
            ->listResponse(
                $usersListView->users,
                Includes::fromArray($includes->toArray()),
                [JsonApi::TOTAL_ITEMS => $totalItems->toInt()],
                $fields->toArray(),
            )
        ;
    }
}
