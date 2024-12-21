<?php

declare(strict_types=1);

namespace Tab\Packages\SqlExpressions;

final readonly class JsonArrayAggregate
{
    public function __construct(
        private string $attribute,
        private bool $distinct = false,
        private ?OrderBy $orderBy = null,
    ) {}

    public function toString(): string
    {
        $distinct = true === $this->distinct
            ? 'DISTINCT '
            : ''
        ;
        $orderBy = '';
        if (null !== $this->orderBy) {
            $orderBy = ' ' . $this->orderBy
                ->toString()
            ;
        }

        if (null === $this->orderBy && false === $this->distinct) {
            return <<<SQL
                JSON_AGG({$this->attribute})
                SQL;
        }

        return <<<SQL
            CAST(
                CONCAT(
                    '[',
                    GROUP_CONCAT(
                        {$distinct}{$this->attribute}{$orderBy}
                    ),
                    ']'
                )
                AS JSON
            )
            SQL;
    }
}
