<?php

declare(strict_types=1);

namespace Tab\UserInterface\Http\Report;

use Symfony\Component\HttpFoundation\Request;
use Tab\Application\Query\MachineReport\MachineReport;
use Tab\Application\View\MachineReportView;
use Tab\Packages\JsonSerializer\JsonSerializerInterface;
use Tab\Packages\MessageBus\Contracts\QueryBusInterface;
use Tab\Packages\Responder\Response\ResponseFactoryInterface;
use Tab\Packages\Responder\Response\ResponseInterface;

final class DownloadMachineReportAction
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private QueryBusInterface $queryBus,
        private JsonSerializerInterface $jsonSerializer,
    ) {}

    public function __invoke(Request $request): ResponseInterface
    {
        /** @var null|array{
         *     machineIds: string[]
         * } $resource
         */
        $resource = $this->jsonSerializer
            ->decode(
                (string) $request->getContent(),
                true,
            )
        ;
        /** @var MachineReportView $data */
        $data = $this->queryBus
            ->handle(new MachineReport($resource['machineIds'] ?? []))
        ;

        return $this->responseFactory
            ->templateResponse(
                'raport.twig.html',
                [
                    'machineReportView' => $data,
                ],
            )
        ;
    }
}
