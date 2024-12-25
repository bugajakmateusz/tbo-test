<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\Snacks;

use OpenApi\Annotations as OA;
use Polsl\Application\Query\SnacksList\SnacksList;
use Polsl\Application\Query\SnacksList\SnacksListView;
use Polsl\Packages\Constants\JsonApi;
use Polsl\Packages\JsonApi\Application\Includes;
use Polsl\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Polsl\Packages\MessageBus\Contracts\QueryBusInterface;
use Polsl\Packages\ResourcesList\QueryParamsExtractorFactory;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class SnacksListAction
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private QueryParamsExtractorFactory $queryParamsExtractorFactory,
    ) {}

    /**
     * List all snacks.
     *
     * @OA\Tag(name="snacks")
     */
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
