<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Machines;

use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Query\MachinesList\MachinesList;
use Tab\Application\Query\MachinesList\MachinesListView;
use Tab\Application\Schema\MachineSchema;
use Tab\Packages\Constants\JsonApi;
use Tab\Packages\JsonApi\Application\Includes;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\QueryBusInterface;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\QueryParamsExtractorFactory;
use Tab\Packages\Responder\Response\ResponseInterface;

final readonly class MachinesListAction
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
        $providedFields = $queryParamsExtractor->fields();
        $this->checkFields($providedFields);

        $projectsListQuery = new MachinesList(
            $filters,
            $queryParamsExtractor->page(),
            $providedFields,
        );
        /** @var MachinesListView $machinesListView */
        $machinesListView = $this->queryBus
            ->handle($projectsListQuery)
        ;

        $totalItems = $machinesListView->totalItems;
        $fields = $machinesListView->fields;

        return $this->jsonApiResponseFactory
            ->listResponse(
                $machinesListView->machines,
                Includes::fromArray($includes->toArray()),
                [JsonApi::TOTAL_ITEMS => $totalItems->toInt()],
                $fields->toArray(),
            )
        ;
    }

    private function checkFields(Fields $providedFields): void
    {
        if (true === $providedFields->isEmpty()) {
            return;
        }
        if (false === $providedFields->hasType(MachineSchema::TYPE)) {
            throw new \RuntimeException(
                "When providing custom fields 'machines' type must be specified.",
            );
        }
    }
}
