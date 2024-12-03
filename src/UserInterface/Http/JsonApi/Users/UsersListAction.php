<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Users;

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
