<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\JsonApi\Machines;

use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Query\MachinesList\MachinesList;
use Tab\Application\Query\MachinesList\MachinesListHandler;
use Tab\Application\Query\MachinesList\MachinesListView;
use Tab\Application\Schema\MachineSchema;
use Tab\Application\View\MachineView;
use Tab\Packages\Collection\ObjectCollection;
use Tab\Packages\JsonApi\Application\Includes;
use Tab\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Tab\Packages\MessageBus\Contracts\QueryBusInterface;
use Tab\Packages\ResourcesList\Fields;
use Tab\Packages\ResourcesList\Filter;
use Tab\Packages\ResourcesList\Filters;
use Tab\Packages\ResourcesList\QueryParamsExtractorFactory;
use Tab\Packages\Responder\Response\ResponseInterface;

final readonly class MachineDetailsAction
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private QueryParamsExtractorFactory $queryParamsExtractorFactory,
    ) {
    }

    public function __invoke(Request $request, int $machineId): ResponseInterface
    {
        $queryParamsExtractor = $this->queryParamsExtractorFactory
            ->fromRequestQueryParams($request)
        ;

        $includes = $queryParamsExtractor->includes();
        $providedFields = $queryParamsExtractor->fields();

        $filters = [
            new Filter(
                MachinesListHandler::FILTER_ID,
                $machineId,
            ),
        ];

        $machinesListQuery = new MachinesList(
            Filters::fromFilters(...$filters),
            $queryParamsExtractor->page(),
            $this->checkFields($providedFields),
        );
        /** @var MachinesListView $machinesListView */
        $machinesListView = $this->queryBus
            ->handle($machinesListQuery)
        ;

        /** @var ObjectCollection<MachineView> $machines */
        $machines = ObjectCollection::fromObjects(...$machinesListView->machines);
        $fields = $machinesListView->fields;

        return $this->jsonApiResponseFactory
            ->resourceResponse(
                $machines->first(),
                Includes::fromArray($includes->toArray()),
                fieldSets: $fields->toArray(),
            )
        ;
    }

    private function checkFields(Fields $providedFields): Fields
    {
        if (true === $providedFields->isEmpty()) {
            return Fields::createFromArray(
                [
                    MachineSchema::TYPE => [
                        MachineSchema::ATTRIBUTE_LOCATION,
                        MachineSchema::ATTRIBUTE_POSITIONS_CAPACITY,
                        MachineSchema::ATTRIBUTE_POSITIONS_NUMBER,
                    ],
                ],
            );
        }

        if (false === $providedFields->hasType(MachineSchema::TYPE)) {
            throw new \RuntimeException(
                "When providing custom fields 'machines' type must be specified.",
            );
        }

        return $providedFields;
    }
}
