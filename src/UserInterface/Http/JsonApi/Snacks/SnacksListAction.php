<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Snacks;

use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Query\SnacksList\SnacksList;
use Tab\Application\Query\SnacksList\SnacksListView;
use Tab\Packages\Constants\JsonApi;
use Tab\Packages\JsonApi\Application\Includes;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\QueryBusInterface;
use Tab\Packages\ResourcesList\QueryParamsExtractorFactory;
use Tab\Packages\Responder\Response\ResponseInterface;

final readonly class SnacksListAction
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
            ->fromRequestQueryParams(
                $request,
            )
        ;
        $includes = $queryParamsExtractor->includes();
        $filters = $queryParamsExtractor->filters();
        $page = $queryParamsExtractor->page();

        $snacksListQuery = new SnacksList(
            $filters,
            $page,
        );
        /** @var SnacksListView $snacksListView */
        $snacksListView = $this->queryBus
            ->handle(
                $snacksListQuery,
            )
        ;

        $totalItems = $snacksListView->totalItems;
        $fields = $snacksListView->fields;

        return $this->jsonApiResponseFactory
            ->listResponse(
                $snacksListView->snacks,
                Includes::fromArray(
                    $includes->toArray(),
                ),
                [
                    JsonApi::TOTAL_ITEMS => $totalItems->toInt(),
                ],
                $fields->toArray(),
            )
        ;
    }
}
