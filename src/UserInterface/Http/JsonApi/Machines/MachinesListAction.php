<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\JsonApi\Machines;

use OpenApi\Annotations as OA;
use Polsl\Application\Query\MachinesList\MachinesList;
use Polsl\Application\Query\MachinesList\MachinesListView;
use Polsl\Application\Schema\MachineSchema;
use Polsl\Packages\Constants\JsonApi;
use Polsl\Packages\JsonApi\Application\Includes;
use Polsl\Packages\JsonApi\ResponseFactory\JsonApiResponseFactoryInterface;
use Polsl\Packages\MessageBus\Contracts\QueryBusInterface;
use Polsl\Packages\ResourcesList\Fields;
use Polsl\Packages\ResourcesList\QueryParamsExtractorFactory;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class MachinesListAction
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private JsonApiResponseFactoryInterface $jsonApiResponseFactory,
        private QueryParamsExtractorFactory $queryParamsExtractorFactory,
    ) {}

    /**
     * List of all machines.
     * This call will return all machines.
     *
     * @OA\Parameter(
     *     name="fields[machines]",
     *     in="query",
     *     required=false,
     *     description="Specify fields to retrieve for machines",
     *
     *     @OA\Schema(
     *        type="string",
     *        example="location,positionsNumber,positionsCapacity"
     *     )
     * ),
     *
     * @OA\Response(
     *     response=204,
     *     description="Returns list with all machines",
     *     content={
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(
     *                         property="totalItems",
     *                         type="number",
     *                     ),
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
     *                             type="string",
     *                             example="machines",
     *                         ),
     *                         @OA\Property(
     *                             property="id",
     *                             type="string",
     *                             example="1",
     *                         ),
     *                         @OA\Property(
     *                             property="attributes",
     *                             type="object",
     *                             @OA\Property(
     *                                 property="location",
     *                                 type="string",
     *                                 example="New York, 7th Street",
     *                             ),
     *                             @OA\Property(
     *                                 property="positionsNumber",
     *                                 type="number",
     *                             ),
     *                             @OA\Property(
     *                                 property="positionsCapacity",
     *                                 type="number",
     *                             ),
     *                         ),
     *                     ),
     *                 ),
     *             )
     *         )
     *     }
     * )
     *
     * @OA\Tag(name="machines")
     */
    public function __invoke(Request $request): ResponseInterface
    {
        $queryParamsExtractor = $this->queryParamsExtractorFactory
            ->fromRequestQueryParams($request)
        ;

        $includes = $queryParamsExtractor->includes();
        $filters = $queryParamsExtractor->filters();
        $providedFields = $queryParamsExtractor->fields();
        $this->checkFields($providedFields);

        $machinesListQuery = new MachinesList(
            $filters,
            $queryParamsExtractor->page(),
            $providedFields,
        );
        /** @var MachinesListView $machinesListView */
        $machinesListView = $this->queryBus
            ->handle($machinesListQuery)
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
