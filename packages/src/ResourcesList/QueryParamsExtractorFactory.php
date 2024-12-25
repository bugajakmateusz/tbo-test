<?php

declare(strict_types=1);

namespace Polsl\Packages\ResourcesList;

use Symfony\Component\HttpFoundation\Request;

final class QueryParamsExtractorFactory
{
    public function fromRequestQueryParams(Request $request): QueryParamsExtractor
    {
        /**
         * @var array{
         *     filter?: null|array<
         *         string,
         *         null|int|mixed[]|string
         *     >,
         *     page?: null|array{
         *         number?: string|int,
         *         size?: string|int,
         *     },
         *     include?: null|string,
         *     sort?: null|string,
         *     fields?: null|array<
         *         string,
         *         string
         *     >,
         * } $queryParams
         */
        $queryParams = $request->query
            ->all()
        ;

        return new QueryParamsExtractor($queryParams);
    }
}
