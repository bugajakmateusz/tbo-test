<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\Machines;

use OpenApi\Annotations as OA;
use Polsl\Application\Query\MachinesList\MachinesList;
use Polsl\Application\Query\MachinesList\MachinesListHandler;
use Polsl\Application\Query\MachinesList\MachinesListView;
use Polsl\Application\Schema\MachineSchema;
use Polsl\Application\View\MachineView;
use Polsl\Packages\Collection\ObjectCollection;
use Polsl\Packages\Constants\HttpStatusCodes;
use Polsl\Packages\JsonApi\Application\Includes;
use Polsl\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Polsl\Packages\MessageBus\Contracts\QueryBusInterface;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\Filter;
use Polsl\Packages\ResourcesList\Filters;
use Polsl\Packages\ResourcesList\QueryParamsExtractorFactory;
use Polsl\Packages\Responder\Response\ResponseFactoryInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class MachineDetailsAction
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private QueryParamsExtractorFactory $queryParamsExtractorFactory,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    /**
     * Machine details.
     *
     * @OA\Get(
     *      path="/api/json-api/machines/{machineId}",
     *      summary="Get details of a specific machine",
     *      description="Returns details of a specific machine with possible included relationships and specific fields.",
     *
     *      @OA\Parameter(
     *          name="machineId",
     *          in="path",
     *          required=true,
     *          description="The ID of the machine to fetch",
     *
     *          @OA\Schema(type="integer", example=161)
     *      ),
     *
     *      @OA\Parameter(
     *          name="fields[machines]",
     *          in="query",
     *          description="Fields of machine to be included in the response",
     *
     *          @OA\Schema(type="string", example="location,positionsNumber,positionsCapacity,machineSnacks")
     *      ),
     *
     *      @OA\Parameter(
     *          name="include",
     *          in="query",
     *          description="Relationships to include in the response",
     *
     *          @OA\Schema(type="string", example="machineSnacks,machineSnacks.snack")
     *      ),
     *
     *      @OA\Parameter(
     *          name="fields[machine-snacks]",
     *          in="query",
     *          description="Fields of machine snacks to be included in the response",
     *
     *          @OA\Schema(type="string", example="quantity,position,snack,price")
     *      ),
     *
     *      @OA\Parameter(
     *          name="fields[snacks]",
     *          in="query",
     *          description="Fields of snacks to be included in the response",
     *
     *          @OA\Schema(type="string", example="name")
     *      ),
     * )
     *
     * @OA\Tag(name="machines")
     */
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

        if (0 === $machines->count()) {
            return $this->responseFactory
                ->jsonResponse(
                    [],
                    HttpStatusCodes::HTTP_NOT_FOUND,
                )
            ;
        }

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
