<?php

declare(strict_types=1);

namespace Polsl\UserInterface\Http\Report;

use Polsl\Application\Query\MachineReport\MachineReport;
use Polsl\Application\View\MachineReportView;
use Polsl\Packages\JsonSerializer\JsonSerializerInterface;
use Polsl\Packages\MessageBus\Contracts\QueryBusInterface;
use Polsl\Packages\Responder\Response\ResponseFactoryInterface;
use Polsl\Packages\Responder\Response\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

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
